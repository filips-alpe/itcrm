<form method="post" action="javascript:SaveNoma()"><input type="image" style="position: absolute; z-index: 200; border: medium none; width: 25px; left: 22px; height: 25px;" value="" name="image" title="Save" src="">
<input name="ID" ID="IDS" type="text" value="[:ID:]" style="display: none"/>
<textarea id="elm1" name="elm1" rows="15" cols="80" style="width: 100%">[:Ligums:]</textarea>
</form>

<script type="text/javascript">
function SaveNoma(){
    var data = $('#elm1').val();
    var IDS =  $('#IDS').val();

    $.ajax({
    type: "POST",
    cache: false,
    url: "/lv/Noma/save",
    data: { Data: data, ID: IDS},
    success: function(data){
        alert("Līgums saglabāts veikmīgi!");
    }
});
}
</script>
