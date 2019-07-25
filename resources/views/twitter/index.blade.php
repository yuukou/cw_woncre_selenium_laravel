 <h2>{{ $today }}のトレンドツイート</h2>
<div class="container">
{{-- コントローラーで取得した$resultをforeachで回す --}}
    @foreach ($result as $tweet)
        <div class="card mb-2">
            <div class="card-body">
                <div class="media">
                    <div class="media-body">
                        <h5 class="d-inline mr-3"><a href="{{ route('detail', ['trend'=> $tweet->query]) }}">{{ $tweet->name }}</a></h5>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0">
                <div class="d-flex flex-row justify-content-end">
                    <div class="mr-5"><i class="far fa-comment text-secondary"></i></div>
                    <div class="mr-5"><i class="fas fa-retweet text-secondary"></i></div>
                    <div class="mr-5"><i class="far fa-heart text-secondary"></i></div>
                </div>
            </div>
        </div>
    @endforeach
</div>
