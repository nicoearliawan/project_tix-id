@extends('Templates.app')

@section('content')
    <div class="container mt-5">
        <h5>Grafik Pembelian Tiket</h5>
        @if (Session::get('success'))
            <div class="alert alert-success"><b>Selamat Datang, {{ Auth::user()->name }}</b></div>
        @endif
        <div class="row mt-4">
            <div class="col-6">
                <h5>Data Pembelian Tiket Bulan {{ now()->format('F') }}</h5>
                <canvas id="chartBar"></canvas>
            </div>
            <div class="col-6">
                <h5>Perbandingan Film Aktif & Non-Aktif</h5>
                <canvas id="chartPie"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        //ajax dipanggil ketika halaman baru selesai di refresh
        $(function() {
            let labelsBar = [];
            let dataBar = [];
            $.ajax({
                url: "{{ route('admin.tickets.chart') }}",
                method: "GET",
                success: function(response) {
                    labelsBar = response.labels; // var labelsBar dari controller json bagian labels
                    dataBar = response.data; // var dataBar dari controller json bagian data
                    //fungsi konfigurasi chart
                    showChartBar();
                },
                error: function(err) {
                    alert('Gagal mengambil data chart!');
                }
            });


            function showChartBar() {
                const ctx = document.getElementById('chartBar');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labelsBar,
                        datasets: [{
                            label: 'Jumlah Tiket Terjual',
                            data: dataBar,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            let dataPie = [];
            $.ajax({
                url: "{{ route('admin.movies.chart') }}",
                method: "GET",
                success: function(response) {
                    dataPie = response.data;
                    showChartPie();
                },
                error: function(err) {
                    alert('Gagal mengambil data chart film!');
                }
            });

            function showChartPie() {
                const ctx = document.getElementById('chartPie');

                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: [
                            'film Aktif',
                            'film Non-Aktif'
                        ],
                        datasets: [{
                            label: 'Perbandingan Data Film Aktif & Non-Aktif',
                            data: dataPie,
                            backgroundColor: [
                                'rgb(54, 162, 235)',
                                'rgb(255, 99, 132)',
                            ],
                            hoverOffset: 4
                        }]
                    }
                });
            }
        });
    </script>
@endpush
