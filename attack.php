<?php

include 'classes/session.php';

Session::init();

include 'classes/database.php';

?>
<!-- begin attack form -->
<div id="loadAttack"> <!-- begin ajax div -->
    <form method="POST">
        <label for="targetname">Target Name</label>
        <input required type="text" name="targetname" id="targetname">
            <br />
        <label for="attack_cost">Attacks (max: 50)</label>
        <input required type="text" name="attack_cost" value="10" id="attack_cost">
            <br />
        <div id="doAttack">Attack!</div>
    </form>
</div> <!-- end ajax div -->


<script type="text/javascript">
/**
* Attack process 
*/
$('#doAttack').click(function()
{
    $('#loading').html('Loading attack...');
    $.ajax({
        type: 'POST',
        url: 'includes/load_attack.php',
        data: 'username='+$('#targetname').val()+'&attack_cost='+$('attack_cost').val(),
        success: function (data)
        {
            $('#loadAttack').html(data);
        }
            
    });
    return false; //default.
});
</script>
