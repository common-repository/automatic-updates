<div class="wrap">
    <h1 class="wp-heading-inline">Manage updates</h1>
    <div class="card">
        <div id="laksh-core-wrapper" class="laksh-spinner-wrapper">
            <div class="spinner"></div>
            <h2 class="title">Choose type of automatic core updates:</h2>
            <p>
                <select id="core-update-type" name="core-update-type">
                    <?php
                    foreach ($coreUpdateTypes as $updateTypeKey=>$coreUpdateLabel):?>
                        <option  <?=($currentCoreUpdateType===$updateTypeKey) ? 'selected': ''?> value="<?=$updateTypeKey?>">
                            <?=$coreUpdateLabel?> </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><label><input type="checkbox" <?=($isNotificationEnabled==true) ? "checked" : ""?> class="update-notification" >Enable email notifications</label>
            </p>
        </div>
    </div>
<br />
    <h2>Manage plugin automatic updates</h2>
<div id="laksh-plugins-wrapper"  class="laksh-spinner-wrapper">
    <div class="spinner"></div>
    <h2 class='screen-reader-text'>Plugins list</h2>
    <table class="wp-list-table widefat fixedx striped pages" style="max-width: 900px">
        <thead>
        <tr>
            <td width="80">Auto udpate</td>
            <th scope="col" class='manage-column '>Plugin</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($plugins as $pluginSlug => $plugin):
            $isIgnored = (!in_array($pluginSlug, $ignoredPlugins));
            ?>
            <tr >
                <th >
                    <input class="js-laksh-plugins-auto-update-input"
                           defaultChecked="<?=($isIgnored)? 'true':'false'?>"
                           <?=($isIgnored) ? 'checked': ''?>
                           id="ch-<?=$pluginSlug?>" type="checkbox" name="post[]" value="<?=$pluginSlug?>"/>
                </th>
                <td class="title">
                    <strong><?=htmlentities($plugin['Name'])?></strong>
                    <p><?=htmlentities($plugin['Description'])?></p>
                </td>
            </tr>
        <?php
        endforeach;
        ?>
        </tbody>
    </table>
</div>
</div>
<p>The automatic update is a background process and it does not interfere with page loads because of this background process.</p>

<style>
    .laksh-spinner-wrapper{
        position: relative;
    }
    .laksh-spinner-wrapper.show .spinner{
        visibility: visible;
    }
    .laksh-spinner-wrapper.show:before{
        display: block;
        content: '';
        background-color: white;
        left: 0; right: 0;
        top: 0; bottom: 0;
        position: absolute;
        opacity: 0.6;
        z-index: 4;
    }
</style>
<script>
(function(){
    var ajaxUrl = "<?=admin_url('admin-ajax.php')?>";
    var isPluginUpdateBusy = false;
    var isCoreUpdateBusy = false;
    var pluginWrapper = document.getElementById('laksh-plugins-wrapper');   //wrapper for plugin inputs
    var coreUpdateWrapper = document.getElementById('laksh-core-wrapper');  //wrapper for core update select
    function laksh_automatic_updates_plugin(pluginSlug, status){
        pluginWrapper.classList.add('show');
        if(isPluginUpdateBusy) return;
        // Set up our HTTP request
        var xhr = new XMLHttpRequest();
        xhr.onload = function () {
            isPluginUpdateBusy = false;
            pluginWrapper.classList.remove('show');
        };
        status = (status) ? 1 : 0;
        xhr.open('GET', ajaxUrl+"?action=laksh_automatic_update_plugin&slug="+pluginSlug+"&status="+status);
        xhr.send();
    }

    function laksh_automatic_updates_core(updateType){
        coreUpdateWrapper.classList.add('show');
        if(isCoreUpdateBusy) return;
        var xhr = new XMLHttpRequest();
        xhr.onload = function () {
            isCoreUpdateBusy = false;
            coreUpdateWrapper.classList.remove('show');
        };
        xhr.open('GET', ajaxUrl+"?action=laksh_automatic_update_core&updateType="+updateType);
        xhr.send();
    }
    function laksh_automatic_updates_notification(isnotificationEnabled){
        coreUpdateWrapper.classList.add('show');
        if(isCoreUpdateBusy) return;
        var xhr = new XMLHttpRequest();
        xhr.onload = function () {
            isCoreUpdateBusy = false;
            coreUpdateWrapper.classList.remove('show');
        };
        xhr.open('GET', ajaxUrl+"?action=laksh_automatic_update_notification&allow="+isnotificationEnabled);
        xhr.send();
    }

    document.querySelectorAll('.js-laksh-plugins-auto-update-input').forEach(function(input){
        input.addEventListener('change', function(event){
            //console.log(event.currentTarget);
            var pluginInput = event.currentTarget;
            console.log(pluginInput.checked);

            laksh_automatic_updates_plugin(pluginInput.value, pluginInput.checked);
        })
    });
    document.getElementById('core-update-type').addEventListener('change', function(event){
        laksh_automatic_updates_core(event.currentTarget.value);
    });

    document.querySelector('.update-notification').addEventListener('change', function(event){
        var notificationVal = (event.currentTarget.checked) ? 1 : 0;
        laksh_automatic_updates_notification(notificationVal);
    });

})();
</script>