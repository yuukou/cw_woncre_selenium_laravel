<script>
    // 削除処理を行なう
    function submitDelete()
    {
        $('#csvUpload').attr('action', '{{
            route('csv::delete')
         }}').submit();
        return false;
    }
</script>
