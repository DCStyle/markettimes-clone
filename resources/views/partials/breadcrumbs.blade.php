@if(isset($items) && count($items) > 0)
<nav class="text-sm text-gray-600 py-3">
    <div class="flex items-center space-x-2">
        @foreach($items as $index => $item)
            @if($index > 0)
                <span class="text-gray-400">/</span>
            @endif

            @if($item['url'])
                <a href="{{ $item['url'] }}" class="hover:text-teal-600 transition-colors">
                    {{ $item['label'] }}
                </a>
            @else
                <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
            @endif
        @endforeach
    </div>
</nav>

@php
$schemaItems = [];
foreach($items as $index => $item) {
    $schemaItem = [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $item['label']
    ];
    if (!empty($item['url'])) {
        $schemaItem['item'] = $item['url'];
    }
    $schemaItems[] = $schemaItem;
}

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $schemaItems
];
@endphp

<script type="application/ld+json">
{!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif
