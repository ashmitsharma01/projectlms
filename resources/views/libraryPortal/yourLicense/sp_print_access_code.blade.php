<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Access Codes</title>
    <style>
        @page {
            size: {{ $getSetting['paper_size'] ?? 0 }};
            margin: 0;
        }


        body {
            margin: 0;
            padding: 0;
            font-family: {{ $getSetting['font_family'] ?? 0 }}, sans-serif;
        }

        .page {
            position: relative;
            height: 100vh;
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .content {
            position: absolute;
            top: {{ $getSetting['margin_from_top'] ?? 0 }}mm;
            left: {{ $getSetting['margin_from_left'] ?? 0 }}mm;
            text-align: left;
        }

        h1 {
            font-size: {{ $getSetting['font_size'] ?? 0 }}px;
            color: {{ $getSetting['font_color'] ?? 0 }};
            margin: 0;
        }
    </style>
</head>

<body>



    @foreach ($accessCodes as $code)
        <div class="page">
            <div class="content">
                <h1>{{ $code->licence_key }}</h1>
                /* <h1>{{ $code->access_code }}</h1> */
            </div>
        </div>
    @endforeach
    <script>
        window.print(); // Trigger the print dialog
    </script>
</body>

</html>
