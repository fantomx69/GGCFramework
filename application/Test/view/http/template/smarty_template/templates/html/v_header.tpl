<HTML>
<HEAD>
<TITLE>{$title}</TITLE>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>

<script type="text/javascript">
function dammiDataOra() {
    $.ajax({
    type: "GET",
    url: "index.php?RequestType=ASYNC&page=index",
    data: "id=1",
    success: function(response){
    $("#output").html(response);
    }
    });
}
</script>

</HEAD>
<BODY bgcolor="#ffffff">
