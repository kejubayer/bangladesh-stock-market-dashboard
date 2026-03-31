@extends('layouts.frontend')

@section('title', 'DSE Live Market')

@push('css')
<style>
    #loading {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1050;
    }

    @media (max-width: 768px) {
        h2 {
            font-size: 1.5rem;
        }

        #refreshBtn {
            font-size: 0.9rem;
            padding: 0.35rem 0.7rem;
        }

        table {
            font-size: 0.85rem;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 1.25rem;
        }

        #refreshBtn {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }

        table {
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h2 class="mb-2 text-center">DSE Live Market Data</h2>

    <div class="d-flex justify-content-between mb-2 align-items-center">
        <div class="d-flex align-items-center">
            <small id="lastUpdate" class="me-3">Last updated: --</small>
            <small id="currentClock">Time: --:--:--</small>
        </div>
        <button id="refreshBtn" class="btn btn-primary btn-sm">Refresh</button>
    </div>

    <div class="position-relative">
        <!-- Loading spinner -->
        <div id="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Table wrapper -->
        <div class="table-responsive" style="overflow-x: auto; min-width: 100%;">
            <table class="table table-bordered table-hover table-striped w-100" id="dseTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Trading Code</th>
                        <th>LTP*</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>CloseP*</th>
                        <th>YCP*</th>
                        <th>Change</th>
                        <th>Trade</th>
                        <th>Value (mn)</th>
                        <th>Volume</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Data will be injected by JS --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
    let dataTable;

    function fetchDseData() {
        $('#loading').show();

        $.getJSON('{{ url("/dse-data") }}', function(data) {
            let tbody = '';
            if (data.length) {
                data.forEach(function(stock, index) {
                    let changeColor = parseFloat(stock.change) >= 0 ? 'text-success' : 'text-danger';
                    tbody += `<tr>
                            <td>${index + 1}</td>
                            <td><a href="${stock.link}" target="_blank">${stock.symbol}</a></td>
                            <td data-order="${parseFloat(stock.ltp.toString().replace(/,/g, ''))}">${stock.ltp}</td>
                            <td data-order="${parseFloat(stock.high.toString().replace(/,/g, ''))}">${stock.high}</td>
                            <td data-order="${parseFloat(stock.low.toString().replace(/,/g, ''))}">${stock.low}</td>
                            <td data-order="${parseFloat(stock.closep.toString().replace(/,/g, ''))}">${stock.closep}</td>
                            <td data-order="${parseFloat(stock.ycp.toString().replace(/,/g, ''))}">${stock.ycp}</td>
                            <td class="${changeColor}" data-order="${stock.change === '--' ? '' : parseFloat(stock.change.toString().replace(/,/g, ''))}">${stock.change === '--' ? '' : stock.change}</td>
                            <td data-order="${parseInt(stock.trade.toString().replace(/,/g, ''))}">${stock.trade}</td>
                            <td data-order="${parseFloat(stock.value.toString().replace(/,/g, ''))}">${stock.value}</td>
                            <td data-order="${parseInt(stock.volume.toString().replace(/,/g, ''))}">${stock.volume}</td>
                        </tr>`;
                });
            } else {
                tbody = `<tr><td colspan="11" class="text-center">No data found</td></tr>`;
            }

            // Destroy old DataTable if exists
            if ($.fn.DataTable.isDataTable('#dseTable')) {
                dataTable.destroy();
            }

            $('#dseTable tbody').html(tbody);

            // Initialize DataTable
            dataTable = $('#dseTable').DataTable({
                pageLength: 15,
                lengthMenu: [
                    [10,15, 50, 100, 200, 300, -1],
                    [10,15, 50, 100, 200, 300, "All"]
                ],
                lengthChange: true,
                searching: true,
                ordering: true,
                order: [
                    [0, 'asc']
                ],
                scrollX: true,
                autoWidth: false,
                responsive: true
            });

            // Update last updated time
            let now = new Date();
            $('#lastUpdate').text('Last updated: ' + now.toLocaleString());

        }).always(function() {
            $('#loading').hide();
        });
    }

    // Live clock
    function updateClock() {
        let now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes().toString().padStart(2, '0');
        let seconds = now.getSeconds().toString().padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        hours = hours.toString().padStart(2, '0');

        $('#currentClock').text(`Time: ${hours}:${minutes}:${seconds} ${ampm}`);
    }


    // Initial fetch
    fetchDseData();
    updateClock(); // initial call

    // Auto update every 60 seconds
    setInterval(fetchDseData, 60000);

    // Clock update every second
    setInterval(updateClock, 1000);

    // Refresh button
    $('#refreshBtn').click(function() {
        fetchDseData();
    });
</script>
@endpush