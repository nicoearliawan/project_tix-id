<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Tiket</title>
    <style>
        .tickets-wrapper {
            width:50%;
            margin-left: 20%;
        }

        .ticket-item {
            width: 340px;
            padding: 10px 22px;
        }

        .studio-title {
            margin: 0;
        }

        .separator {
            border: none;
            background: rgb(0,0,0,0.2);
            height: 1px;
            margin: 10px 0;
        }

        .ticket-title {
            font-weight: bold;
            margin: 0 0 8px 0;
        }

        .ticket-details small {
            font-weight: bold;
            display: inline-block;
            width: 60px;
        }
    </style>
</head>

<body>
    <div class="tickets-wrapper">
        @foreach ($ticket['rows_of_seats'] as $item)
            <div class="ticket-item">
                <div class="ticker-header">
                    <div><b>{{ $ticket['schedule']['cinema']['name'] }}</b></div>
                    <div>
                        <h5c class="studio-title">STUDIO</h5>
                    </div>
                    <hr class="separator">
                    <div class="ticket-body">
                        <p class="ticket-title">{{ $ticket['schedule']['movie']['title'] }}</p>
                        <div class="ticket-detail">
                            <small>Tanggal:</small>{{
                            \Carbon\Carbon::parse($ticket['ticket_payment']['booked_date'])->format('d F, Y ') }}<br>
                            <small>Waktu:</small>{{ \Carbon\Carbon::parse($ticket['hour'])->format('H:i') }}<br>
                            <small>Kursi:</small>{{ $item }}<br>
                            <small>Price:</small>Rp. {{ number_format($ticket['schedule']['price']) }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
