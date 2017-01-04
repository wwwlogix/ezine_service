<div id="content">
    <div class="inner">
        <div class="clear"></div>
        <div class="onecolumn" >
            <div class="header">
                <span>
                    <span class="ico gray window"></span>  <?php  echo $title?>
                </span>
            </div>
            <div class="clear"></div>
            <div class="content" >
                <?php
                if($category_edit!=""){
                    $result1 = $category_edit->row();
                ?>
                <form id="demo" action="<?php echo base_url() ?>en_main/update_forum_categories" method="post" enctype="multipart/form-data" >
                <input type="hidden" value="<?php echo $result1->id?>" id="" name="id">
                <?php   }else{ ?>
                    <form id="demo" action="<?php echo base_url() ?>en_main/save_forum_categories" method="post" enctype="multipart/form-data" >
                    <?php }?>
                    <div class="section" >
                        <label> Categories Title </label>
                        <div>
                            <input type="text" name="categories_title" id="location"  class=" full" value="<?php echo $result1->name;?>"/>
                        </div>
                    </div>
                    <div class="section" >
                        <label> Categories Description</label>
                        <div>
                            <textarea name="forum_desc" id="forum_desc" style="width: 95%"><?php echo $result1->description; ?></textarea>
                        </div>
                    </div>
                    <div class="section" >
                        <label> Categories Postion</label>
                        <div>
                                <input type="text" name="categories_postion" id="location"  class=" full" value="<?php echo $result1->position; ?>"/>
                        </div>
                    </div>
                    <div class="section last">
                        <div>
                            <input type="submit" class="uibutton loading" value="Submit">
                        </div>
                    </div>
                </form>
                <table id="locations" class="display categories dataTable">
                    <thead>
                    <tr role="row">
                        <th width="35" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1" style="width: 34px;" aria-label="&lt;input id=&quot;checkAll1&quot; class=&quot;checkAll&quot; type=&quot;checkbox&quot;&gt;">
                            <div class="custom-checkbox">
                                <input type="checkbox" class="checkAll" id="checkAll1">
                                <label class="checker" for="checkAll1" style="left: 3px;"></label>
                            </div>
                        </th>
                        <th width="352" align="left" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1" style="width: 320px;" aria-label="Name">User Name</th>
                        <th width="174" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1" style="width: 159px;" aria-label="Type">Status</th>
                        <th width="199" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1" style="width: 187px;" aria-label="Management"></th>
                    </tr>
                    </thead>
                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                    <?php
                    $i=1;
                    $number = 20;
                    foreach($forum_categories->result() as $categories)
                    {
                        $i++;
                        $number;
                        if ($number % 2 == 0) {
                            $class_name = "even";
                        }
                        else
                        {
                            $class_name="odd";
                        }
                    ?>
                    <tr class="<?php echo $class_name ?>">
                        <td width="35" class=" ">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="check<?php echo $i;  ?>" class="chkbox" name="checkbox[]">
                                <label class="checker" for="check<?php echo $i;  ?>" style="left: 3px;"></label>
                            </div>
                        </td>
                        <td align="left" class=" ">
                            <a href="<?php echo base_url() ?>en_main/forum/<?php echo $categories->id?>">
                                <?php echo $categories->name?>
                            </a>
                        </td>
                        <td class="">
                            <?php echo $categories->description?>
                        </td>
                        <td class="">
                          <span class="tip">
                              <a title="Edit" href="<?php echo base_url();  ?>en_main/edit_forum_categories/<?php echo $categories->id  ?>">
                                  <img src="<?php echo base_url() ?>assets/images/icon/icon_edit.png">
                              </a>
                          </span>
                          <span class="tip">
                              <a title="Delete" name=" <?php echo $categories->name?>" class="Delete" id="<?php echo $categories->id  ?>" table="categories" col="id">
                                  <img src="<?php echo base_url() ?>assets/images/icon/icon_delete.png">
                              </a>
                          </span>
                        </td>
                    </tr>
                <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!--// End inner -->
</div> <!--// End content -->