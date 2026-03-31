@extends('layouts.frontend')

@section('title', 'Live Market')

@push('css')
<style>
    .market-table-wrapper {
        height: 700px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .market-table-wrapper table {
        width: 100%;
        margin-bottom: 0;
    }

    .market-table-wrapper th,
    .market-table-wrapper td {
        text-align: center;
        vertical-align: middle;
        padding: 0.35rem;
        font-size: 0.85rem;
    }

    .text-success {
        color: #28a745;
    }

    .text-danger {
        color: #dc3545;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .table-header h4 {
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <!-- DSE Table -->
        <div class="col-md-6 mb-3">
            <div class="table-header">
                <h4>DSE Live Market Data</h4>
                <div>
                    <small id="dseLastUpdate">Last updated: --</small>
                    <button class="btn btn-sm btn-primary ms-2" id="dseRefreshBtn">Refresh</button>
                </div>
            </div>
            <div class="market-table-wrapper">
                <table class="table table-bordered table-hover table-striped">
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
                    <tbody id="dseTableBody">
                        <tr>
                            <td colspan="11" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CSE Table -->
        <div class="col-md-6 mb-3">
            <div class="table-header">
                <h4>CSE Live Market Data</h4>
                <div>
                    <small id="cseLastUpdate">Last updated: --</small>
                    <button class="btn btn-sm btn-primary ms-2" id="cseRefreshBtn">Refresh</button>
                </div>
            </div>
            <div class="market-table-wrapper">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Stock Code</th>
                            <th>LTP*</th>
                            <th>Open</th>
                            <th>High</th>
                            <th>Low</th>
                            <th>YCP*</th>
                            <th>Change</th>
                            <th>Trade</th>
                            <th>Value (mn)</th>
                            <th>Volume</th>
                        </tr>
                    </thead>
                    <tbody id="cseTableBody">
                        <tr>
                            <td colspan="11" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function fetchDseData() {
        $.getJSON("{{ route('dse.fetch') }}", function(res) {
            let tbody = '';
            if (res.length) {
                res.forEach((stock, index) => {
                    let changeColor = parseFloat(stock.change) >= 0 ? 'text-success' : 'text-danger';
                    tbody += `<tr>
                        <td>${index + 1}</td>
                        <td><a href="${stock.link}" target="_blank">${stock.symbol}</a></td>
                        <td>${stock.ltp}</td>
                        <td>${stock.high}</td>
                        <td>${stock.low}</td>
                        <td>${stock.closep ?? '--'}</td>
                        <td>${stock.ycp}</td>
                        <td class="${changeColor}">${stock.change}</td>
                        <td>${stock.trade}</td>
                        <td>${stock.value}</td>
                        <td>${stock.volume}</td>
                    </tr>`;
                });
            } else {
                tbody = '<tr><td colspan="11" class="text-center">No data found</td></tr>';
            }
            $('#dseTableBody').html(tbody);
            $('#dseLastUpdate').text('Last updated: ' + new Date().toLocaleTimeString());
        });
    }

    function fetchCseData() {
        $.getJSON("{{ route('cse.fetch') }}", function(res) {
            let tbody = '';
            if (res.length) {
                res.forEach((stock, index) => {
                    let changeColor = parseFloat(stock.change) >= 0 ? 'text-success' : 'text-danger';
                    tbody += `<tr>
                        <td>${index + 1}</td>
                        <td><a href="${stock.link}" target="_blank">${stock.symbol}</a></td>
                        <td>${stock.ltp}</td>
                        <td>${stock.open}</td>
                        <td>${stock.high}</td>
                        <td>${stock.low}</td>
                        <td>${stock.ycp}</td>
                        <td class="${changeColor}">${stock.change}</td>
                        <td>${stock.trade}</td>
                        <td>${stock.value}</td>
                        <td>${stock.volume}</td>
                    </tr>`;
                });
            } else {
                tbody = '<tr><td colspan="11" class="text-center">No data found</td></tr>';
            }
            $('#cseTableBody').html(tbody);
            $('#cseLastUpdate').text('Last updated: ' + new Date().toLocaleTimeString());
        });
    }

    function fetchAllData() {
        fetchDseData();
        fetchCseData();
    }

    $(document).ready(function() {
        fetchAllData();

        // Auto refresh every 1 minute
        setInterval(fetchAllData, 60000);

        // Manual refresh buttons
        $('#dseRefreshBtn').click(fetchDseData);
        $('#cseRefreshBtn').click(fetchCseData);
    });
</script>
@endpush