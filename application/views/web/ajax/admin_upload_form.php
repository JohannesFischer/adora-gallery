<form method="post" action="<?=site_url('upload/do_upload');?>" enctype="multipart/form-data" class="upload"> 
    <fieldset> 
        <legend>Upload Files</legend> 
     
        <div class="formRow"> 
            <label for="url" class="floated">File: </label> 
            <input type="file" id="url" name="url" multiple><br> 
        </div> 
     
        <div class="formRow"> 
            <input type="submit" name="upload" value="Upload"> 
        </div> 
     
    </fieldset>
</form>