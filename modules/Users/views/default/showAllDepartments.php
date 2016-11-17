<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Location;
?>
<div class="user-view">
    <span class="pull-right">
        <a href="javascript://" class="btn btn-success addNewDepartment">Add New Department</a>
    </span> 
    <br><br><br>
    <?php if(isset($model) && !empty($model) && count($model) > 0){ ?>
    <table class="table table-striped table-bordered detail-view" id="w0">
        
        <tbody>
            <?php for($i=count($model)-1;$i>0;$i--){?>
            <tr>
                <td><?php echo $model[$i]['name'];?></td>
                 <td><a href="javascript://" did="<?php echo $model[$i]['id'];?>" class="btn-xs btn-primary editDepartment">Edit</a></td>
            </tr>
            
            <?php } ?>
        </tbody>
    </table>
    <?php }else { 
        
        echo "No records are found!";
        
    } ?>

</div>
