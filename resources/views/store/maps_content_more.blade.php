@if($bottom_list)
    @foreach($bottom_list as $key => $item)
        <li><a href="{{ route('store.mapscontent_detail', ['content' => $item->id]) }}">
            <h2><img src="{{ Storage::url($item->img) }}"></h2>
            <h3>{{ $item->title }}</h3>
            <p>{!! $item->description !!}</p>
            <span>{{ $item->created_at->format('Y.m.d') }}</span></a>
        </li>
    @endforeach
@endif
