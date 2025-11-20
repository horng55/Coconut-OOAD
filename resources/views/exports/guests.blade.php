<table>
    <thead>
    <tr>
        <th>ឈ្មោះភ្ញៀវ</th>
        <th>ចងដៃតាមរយ</th>
        <th>ដុល្លារ</th>
        <th>រៀល</th>
        <th>ចំណាំ</th>
    </tr>
    </thead>
    <tbody>
    @foreach($guests as $guest)
        <tr>
            <td>{{ $guest->invite_name }}</td>
            <td>  {{ $guest->payment_option === 'cash' ? 'សាច់ប្រាក់' : ($guest->payment_option === 'bank' ? 'ធនាគារ' : '-') }}</td>
            <td>{{ $guest->amount_usd }}</td>
            <td>{{ $guest->amount_khr }}</td>
            <td>{{ $guest->description }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
