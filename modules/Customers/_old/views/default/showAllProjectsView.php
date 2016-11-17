<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Location;
?>
<div class="user-view">
    <?php if(isset($model) && !empty($model) && count($model) > 0){ ?>
    <table class="table table-striped table-bordered detail-view" id="w0">
        <tbody>
            <?php for($i=0;$i<count($model);$i++){?>
            <tr>
                <td><?php echo $model[$i]['companyname'];?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php }else { 
        
        echo "No records are found!";
        
    } ?>

</div>
