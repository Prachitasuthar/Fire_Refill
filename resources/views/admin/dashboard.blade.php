@extends('admin-layouts.app')

@section('title', 'About Us')

@section('content')

    <div class="page-wrapper">
        <div class="page-content-tab">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="float-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">RefillEase</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Analytics</li>
                                </ol>
                            </div>
                            <h4 class="page-title">Analytics</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-9">
                        <div class="row justify-content-center">
                            <div class="col-md-6 col-lg-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-9">
                                                <p class="text-dark mb-0 fw-semibold">Total Users</p>
                                                <h3 class="my-1 font-20 fw-bold">{{ $totalUsers }}</h3>
                                                <p class="mb-0 text-truncate text-muted">
                                                    <span class="text-success">
                                                        <i class="mdi mdi-account-check"></i>
                                                        Active Users
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col-3 align-self-center">
                                                <div
                                                    class="d-flex justify-content-center align-items-center thumb-md bg-light-alt rounded-circle mx-auto">
                                                    <i class="mdi mdi-account-check text-muted"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-9">
                                                <p class="text-dark mb-0 fw-semibold">Total Service Providers</p>
                                                <h3 class="my-1 font-20 fw-bold">{{ $totalProviders }}</h3>
                                                <p class="mb-0 text-truncate text-muted">
                                                    <span class="text-success">
                                                        <i class="mdi mdi-account-group"></i> Approved Providers
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col-3 align-self-center">
                                                <div
                                                    class="d-flex justify-content-center align-items-center thumb-md bg-light-alt rounded-circle mx-auto">
                                                    <i class="mdi mdi-account-group text-muted"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-9">
                                                <p class="text-dark mb-0 fw-semibold">Total Service Requests</p>
                                                <h3 class="my-1 font-20 fw-bold">{{ $totalServiceRequests }}</h3>
                                                <p class="mb-0 text-truncate text-muted">
                                                    <span class="text-success">
                                                        <i class="mdi mdi-clipboard-list-outline"></i> Pending & Completed
                                                        Requests
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col-3 align-self-center">
                                                <div
                                                    class="d-flex justify-content-center align-items-center thumb-md bg-light-alt rounded-circle mx-auto">
                                                    <i class="mdi mdi-clipboard-list-outline text-muted"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-9">
                                                <p class="text-dark mb-0 fw-semibold">Total Arrived Orders</p>
                                                <h3 class="my-1 font-20 fw-bold">{{ $totalArrivedOrders }}</h3>
                                                <p class="mb-0 text-truncate text-muted">
                                                    <span class="text-success">
                                                        <i class="mdi mdi-package-variant-closed"></i> Orders Successfully
                                                        Delivered
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col-3 align-self-center">
                                                <div
                                                    class="d-flex justify-content-center align-items-center thumb-md bg-light-alt rounded-circle mx-auto">
                                                    <i class="ti ti-confetti font-24 align-self-center text-muted"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Monthly Refill Requests</h4>
                            </div>
                            <div class="card-body">
                                <div id="refill_chart"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">🔥 Fire Refill Service Requests</h4>
                                    </div>
                                </div> <!--end row-->
                            </div><!--end card-header-->

                            <div class="card-body">
                                <div class="text-center">
                                    <div id="fire_refill_chart" class="apex-charts"></div>
                                    <h6 class="bg-light-alt py-3 px-2 mb-0">
                                        <i data-feather="calendar" class="align-self-center icon-xs me-1"></i>
                                        {{ now()->startOfYear()->format('d M Y') }} to
                                        {{ now()->endOfYear()->format('d M Y') }}
                                    </h6>
                                </div>

                                <div class="table-responsive mt-2">
                                    <table class="table border-dashed mb-0">
                                        <thead>
                                            <tr>
                                                <th>Provider</th>
                                                <th class="text-end">Total Requests</th>
                                                <th class="text-end">Today</th>
                                                <th class="text-end">This Week</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($fireRefillStats as $data)
                                                <tr>
                                                    <td>{{ $data->provider->first_name }} {{ $data->provider->last_name }}
                                                    </td>
                                                    <td class="text-end">{{ $data->total_requests }}</td>
                                                    <td class="text-end">{{ $data->today_requests }}</td>
                                                    <td class="text-end">{{ $data->week_requests }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div><!--end /table-->
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div> <!--end col-->
                </div><!--end row-->

                <div class="row">
                    <!-- Live Product Stats -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Live Product Stats</h4>
                            </div>
                            <div class="card-body">
                                <div id="productChart" class="apex-charts"></div>
                                <div class="d-flex justify-content-between align-items-start">
                                    <!-- Left Side: Product Categories Title -->
                                    <h4 class="card-title mt-0 mb-2">Total Product: {{ $totalProducts ?? 0 }} </h4>

                                    {{-- <div class="card-title mt-0 mb-2">
                                        <h4 class="my-2">{{ $totalProducts ?? 0 }}</h4>
                                        <p class="card-title mt-0 mb-2">Total Products</p>
                                    </div> --}}


                                    <!-- Categories (Kept in List Format) -->
                                    <ul class="list-unstyled url-list mb-0">
                                        <li><i class="fas fa-caret-right font-16 text-primary"></i> Accessories:
                                            {{ $categoryCounts['accessories'] ?? 0 }}</li>
                                        <li><i class="fas fa-caret-right font-16 text-danger"></i> Fire Extinguishers:
                                            {{ $categoryCounts['fire_extinguishers'] ?? 0 }}</li>
                                        <li><i class="fas fa-caret-right font-16 text-success"></i> Watermist:
                                            {{ $categoryCounts['watermist'] ?? 0 }}</li>
                                        <li><i class="fas fa-caret-right font-16 text-warning"></i> Fire Suppression:
                                            {{ $categoryCounts['fire_suppression'] ?? 0 }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <!-- Low Stock Alert -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">⚠️ Low Stock Alert</h4>
                            </div>
                            <div class="card-body">
                                <div id="lowStockChart"></div>
                            </div>
                        </div>
                    </div>
                </div> --}}


                    <!-- Low Stock Alert -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">⚠️ Low Stock Alert</h4>
                            </div>
                            <div class="card-body">
                                <div id="lowStockChart"></div>
                            </div>
                        </div>
                    </div>

                   
                    @if (!empty($lowStockItems) && count($lowStockItems) > 0)
                        <script>
                            var lowStockData = @json($lowStockItems);
                        </script>
                    @else
                        <script>
                            var lowStockData = [];
                        </script>
                    @endif




                    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


                    <script>
                        // monthly refill request
                        document.addEventListener("DOMContentLoaded", function() {
                            let data = @json($monthlyServiceRequests);
                            let months = {
                                1: "Jan",
                                2: "Feb",
                                3: "Mar",
                                4: "Apr",
                                5: "May",
                                6: "Jun",
                                7: "Jul",
                                8: "Aug",
                                9: "Sep",
                                10: "Oct",
                                11: "Nov",
                                12: "Dec"
                            };

                            let chartLabels = [];
                            let chartData = [];

                            for (let i = 1; i <= 12; i++) {
                                let value = data[i] || 0;
                                chartLabels.push(months[i]);
                                chartData.push(value);
                            }

                            let options = {
                                chart: {
                                    type: "bar",
                                    height: 350
                                },
                                series: [{
                                    name: "Refill Requests",
                                    data: chartData
                                }],
                                xaxis: {
                                    categories: chartLabels
                                },
                                plotOptions: {
                                    bar: {
                                        columnWidth: '30%',
                                        borderRadius: 4,
                                        dataLabels: {
                                            position: "top"
                                        }
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    formatter: function(value) {
                                        return value > 1 ? value : '';
                                    },
                                    style: {
                                        colors: ["#000"]
                                    }
                                }
                            };


                            // service request
                            document.querySelector("#refill_chart").innerHTML = "";
                            let chart = new ApexCharts(document.querySelector("#refill_chart"), options);
                            chart.render();
                        });

                        document.addEventListener("DOMContentLoaded", function() {
                            var chartData = @json($chartData);
                            var labels = chartData.map(data => data.provider);
                            var dataValues = chartData.map(data => data.total);

                            if (window.pieChart !== undefined) {
                                window.pieChart.destroy();
                            }

                            document.querySelector("#fire_refill_chart").innerHTML = "";

                            var options = {
                                chart: {
                                    type: 'donut',
                                    height: 350
                                },
                                labels: labels,
                                series: dataValues,
                                colors: ['#FF4560', '#008FFB', '#FEB019', '#00E396', '#775DD0'],
                                legend: {
                                    position: 'right',
                                    horizontalAlign: 'center',
                                    offsetX: 0
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '70%'
                                        }
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    formatter: function(val, opts) {
                                        return val.toFixed(1) + "%";
                                    }
                                }
                            };


                            window.pieChart = new ApexCharts(document.querySelector("#fire_refill_chart"), options);
                            window.pieChart.render();
                        });



                        // live product stats
                        document.addEventListener("DOMContentLoaded", function() {
                            var options = {
                                series: [
                                    {{ $categoryCounts['accessories'] ?? 0 }},
                                    {{ $categoryCounts['fire_extinguishers'] ?? 0 }},
                                    {{ $categoryCounts['watermist'] ?? 0 }},
                                    {{ $categoryCounts['fire_suppression'] ?? 0 }}
                                ],
                                chart: {
                                    type: 'donut',
                                    height: 350
                                },
                                labels: ['Accessories', 'Fire Extinguishers', 'Watermist', 'Fire Suppression'],
                                colors: ['#007bff', '#dc3545', '#28a745', '#ffc107']
                            };

                            var chart = new ApexCharts(document.querySelector("#productChart"), options);
                            chart.render();
                        });



                        // low stock
                        document.addEventListener("DOMContentLoaded", function() {
                            setTimeout(function() {
                                renderLowStockChart();
                            }, 500); 
                        });

                        function renderLowStockChart() {
                            if (window.lowStockChartInstance) {
                                window.lowStockChartInstance.destroy();
                            }

                            if (lowStockData.length === 0) {
                                document.getElementById("lowStockChart").innerHTML =
                                    "<p class='text-center text-danger fw-bold'>✅ No low-stock products found.</p>";
                                return;
                            }

                            var options = {
                                series: lowStockData.map(item => item.stock),
                                chart: {
                                    type: 'donut',
                                    height: 400
                                },
                                labels: lowStockData.map(item => item.name),
                                colors: ['#FF4560', '#FFA41B', '#775DD0', '#00E396', '#008FFB'],
                                legend: {
                                    position: 'bottom'
                                },
                                dataLabels: {
                                    enabled: true,
                                    formatter: function(val, opts) {
                                        return opts.w.config.series[opts.seriesIndex] + " left";
                                    }
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '65%',
                                            labels: {
                                                show: true,
                                                name: {
                                                    show: true
                                                },
                                                value: {
                                                    show: true
                                                }
                                            }
                                        }
                                    }
                                },
                                tooltip: {
                                    y: {
                                        formatter: function(val) {
                                            return val + " units remaining";
                                        }
                                    }
                                }
                            };

                            window.lowStockChartInstance = new ApexCharts(document.querySelector("#lowStockChart"), options);
                            window.lowStockChartInstance.render();
                        }
                    </script>
                @endsection
