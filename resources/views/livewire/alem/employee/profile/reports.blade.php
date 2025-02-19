<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Employee Reports</h1>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @foreach ($dummyReports as $report)
            <tr>
                <td class="px-4 py-2 whitespace-nowrap">{{ $report['id'] }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $report['title'] }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $report['description'] }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $report['date'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
