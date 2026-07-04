<html>
<head>
    <title>Asset Report</title>
    <style>
        table{
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;

        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

<h2>Asset List Report</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Status</th>
            <th>Price</th>
            <th>Category</th>
            {{-- <th>Warranty Expiry</th> --}}
        </tr>
    </thead>

    <tbody>
        @foreach($assets as $asset)
            <tr>
                <td>{{ $asset->id }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->brand }}</td>
                <td>{{ $asset->status }}</td>
                <td>{{ $asset->price }}</td>
                <td>{{ $asset->category?->name }}</td>
                {{-- <td>{{ $asset->Warranty }}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>