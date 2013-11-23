var counter=1;
function extend(fields,limit)
{
    var formholder;
    if(fields == 1){
        formholder = '<p><label for="naam">name image</label><input id="naam" name="naam[]" type="text"/></p><p><label for="image">file image</label><input id="image" name="image[]" type="file"/></p>';
    }else{
        formholder = '<p><label for="image">file image</label><input id="image" name="image[]" type="file"/></p>';
    }
    //var newdiv = document.createElement('div');
    //newdiv.innerHTML = formholder;
    //document.getElementById('commentform').appendchild(newdiv);
    if(counter<limit){
        document.getElementById('commentform').innerHTML = document.getElementById('commentform').innerHTML + formholder;
        counter++;
    }
    
}