<form action="/testform" method="post">
    {{ csrf_field() }}
    <p>"cat ../../../storage/exports/* > cat ../../../storage/exports/combined.csv"</p>
    <input type="text" name="cmd">
    <button type="submit">senden</button>
</form>