{{-- Todo: tanaka bootstrapを使用するのが練習にもなるし、レスポンシブにもなってよいのでは？？ --}}
<div>
    <form method="post" action="{{ route('csv::post') }}" enctype="multipart/form-data" id="csvUpload">
        <input type="file" value="ファイルを選択" name="csv_file">
        {{ csrf_field() }}
        <button type="submit">インポート</button>
    </form>
</div>
