<style>
    @font-face {
        font-family: 'Garamond';
        font-style: normal;
        font-weight: 400;
        src: url('{{ url("font/EBGaramond.otf") }}') format('opentype');
    }
    body {
        font-family: "Garamond", Helvetica, Arial, sans-serif;
    }
</style>

<h1>
    A new bug was reported!
</h1>

<table>
    <tr>
        <td><b>Contact Info</b></td>
        <td>{{ $contactinfo }}</td>
    </tr>
    <tr>
        <td><b>Description</b></td>
        <td>{{ $description }}</td>
    </tr>
</table>