@foreach($articles as $article)
    <x-article-card :article="$article" layout="horizontal" />
@endforeach
