<div id="content">
    <div class="inner">
        <div class="clear"></div>
        <div class="onecolumn" >
            <div class="header">
                <span>
                    <span class="ico gray window"></span>  <?php
                    $result=$cate_name->row();
                    echo $result->name?>'s Topics List
                </span>
            </div>
            <div class="clear"></div>
            <div class="content" >
                <form id="demo" action="<?php echo base_url() ?>en_main/save_topic" method="post" enctype="multipart/form-data" >
                    <input type="hidden" value="<?php echo $category_id?>" id="" name="id">
                        <div class="section" >
                            <label> Topic Title </label>
                            <div>
                                <input type="text" name="topic_title" id="topic_title"  class=" full"/>
                            </div>
                        </div>
                        <div class="section" >
                            <label> Categories Postion</label>
                            <div>
                                <textarea name="topic_desc" id="topic_desc" style="width: 95%"></textarea>
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
                    foreach($topics->result() as $topics_list)
                    {
                        $i++;
                        $number;
                        if ($number % 2 == 0)
                        {
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

                            <?php echo $topics_list->title?>
                        </td>
                        <td class="">
                            <?php echo $topics_list->message?>
                        </td>
                        <td class="">
<!--                          <span class="tip">-->
<!--                              <a title="Edit" href="--><?php //echo base_url();  ?><!--en_main/edit_forum_categories/--><?php //echo $topics_list->id  ?><!--">-->
<!--                                  <img src="--><?php //echo base_url() ?><!--assets/images/icon/icon_edit.png">-->
<!--                              </a>-->
<!--                          </span>-->
                          <span class="tip">
                              <a title="Delete" name=" <?php echo $topics_list->title?>" class="Delete" id="<?php echo $topics_list->id  ?>" table="topics" col="id">
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