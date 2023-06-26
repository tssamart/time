<?php

/**
 * Template
 */

defined('RESTRICTED') or die('Restricted access');

$canvasName = 'goal';
$elementName = 'goal';


/**
 * showCanvasTop.inc template - Top part of the main canvas page
 *
 * Required variables:
 * - $canvasName       Name of current canvas
 */

$canvasTitle = '';
$allCanvas = $this->get('allCanvas');
$canvasIcon = $this->get('canvasIcon');
$canvasTypes = $this->get('canvasTypes');
$statusLabels = $statusLabels ?? $this->get('statusLabels');
$relatesLabels = $relatesLabels ?? $this->get('relatesLabels');
$dataLabels = $this->get('dataLabels');
$disclaimer = $this->get('disclaimer');
$canvasItems = $this->get('canvasItems');

$filter['status'] = $_GET['filter_status'] ?? ($_SESSION['filter_status'] ?? 'all');
$_SESSION['filter_status'] = $filter['status'];
$filter['relates'] = $_GET['filter_relates'] ?? ($_SESSION['filter_relates'] ?? 'all');
$_SESSION['filter_relates'] = $filter['relates'];

//get canvas title
foreach ($this->get('allCanvas') as $canvasRow) {
    if ($canvasRow["id"] == $this->get('currentCanvas')) {
        $canvasTitle = $canvasRow["title"];
        break;
    }
}

$goalStats = $this->get("goalStats");

?>
<style>
    .canvas-row { margin-left: 0px; margin-right: 0px;}
    .canvas-title-only { border-radius: var(--box-radius-small); }
    h4.canvas-element-title-empty { background: white !important; border-color: white !important; }
    div.canvas-element-center-middle { text-align: center; }
</style>

<div class="pageheader">
    <div class="pageicon"><span class='fa <?=$canvasIcon ?>'></span></div>
    <div class="pagetitle">
        <h5><?php $this->e($_SESSION['currentProjectClient'] . " // " . $_SESSION['currentProjectName']); ?></h5>

        <h1><?=$this->__("headline.$canvasName.dashboardboard") ?>

        </h1>
    </div>
</div><!--pageheader-->

<div class="maincontent">


    <?php echo $this->displayNotification(); ?>



    <div class="row" style="margin-bottom:20px; ">
            <div class="col-md-4">
                <div class="bigNumberBox">
                    <h2>Progress: <?php echo round($goalStats['avgPercentComplete']); ?>%</h2>

                    <div class="progress" style="margin-top:5px;">
                        <div class="progress-bar progress-bar-success" role="progressbar"
                             aria-valuenow="<?php echo round($goalStats['avgPercentComplete']); ?>" aria-valuemin="0" aria-valuemax="100"
                             style="width: <?php echo $goalStats['avgPercentComplete']; ?>%">
                            <span class="sr-only"><?=sprintf($this->__("text.percent_complete"), round($goalStats['avgPercentComplete']))?></span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-2">
            </div>
            <div class="col-md-2">
                <div class="bigNumberBox priority-border-4">
                    <h2>On Track</h2>
                    <span class="content"><?=$goalStats['goalsOnTrack'] ?></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="bigNumberBox priority-border-3">
                    <h2>At Risk</h2>
                    <span class="content"><?=$goalStats['goalsAtRisk'] ?></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="bigNumberBox priority-border-1">
                    <h2>Miss</h2>
                    <span class="content"><?=$goalStats['goalsMiss'] ?></span>

                </div>

            </div>
        </div>




    <div class="maincontentinner">
        <div class="row">
            <div class="col-md-6">

                <a href="javascript:void(0)" class="addCanvasLink btn btn-primary"><?=$this->__("links.icon.create_new_board") ?></a>
                <br /><br />
            </div>
        </div>
        <?php if (count($allCanvas) > 0) {?>

            <?php
                foreach ($this->get('allCanvas') as $canvasRow) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo "<h5 class='subtitle'><a href='" . BASE_URL . "/" . $canvasName . "canvas/showCanvas/" . $canvasRow["id"] . "'>" . $this->escape($canvasRow["title"]) . "</a></h5>"; ?>
                        </div>
                    </div>
                    <div class="row" style="border-bottom:1px solid var(--main-border-color); margin-bottom:20px">
                        <?php
                            $canvasSvc = new \leantime\domain\repositories\goalcanvas();
                            $canvasItems = $canvasSvc->getCanvasItemsById($canvasRow["id"]);
                            ?>
                            <div id="sortableCanvasKanban-<?=$canvasRow["id"]?>" class="sortableTicketList disabled col-md-12" style="padding-top:15px;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">

                                            <?php if(!is_countable($canvasItems) || count($canvasItems) == 0) {
                                                echo "<div class='col-md-12'>No goals on this board yet. Open the <a href='" . BASE_URL . "/" . $canvasName . "canvas/showCanvas/" . $canvasRow["id"] . "'>board</a> to start adding goals</div>";
                                            }
                                            ?>
                                            <?php foreach ($canvasItems as $row) {
                                                $filterStatus = $filter['status'] ?? 'all';
                                                $filterRelates = $filter['relates'] ?? 'all';

                                                if (
                                                    $row['box'] === $elementName && ($filterStatus == 'all' ||
                                                    $filterStatus == $row['status']) && ($filterRelates == 'all' ||
                                                    $filterRelates == $row['relates'])
                                                ) {
                                                    $comments = new \leantime\domain\repositories\comments();
                                                    $nbcomments = $comments->countComments(moduleId: $row['id']);
                                                    ?>
                                                <div class="col-md-4">
                                                    <div class="ticketBox" id="item_<?php echo $row["id"];?>">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="inlineDropDownContainer" style="float:right;">

                                                                <?php if ($login::userIsAtLeast($roles::$editor)) { ?>
                                                                    <a href="javascript:void(0)" class="dropdown-toggle ticketDropDown" data-toggle="dropdown">
                                                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                                    </a>
                                                                <?php } ?>


                                                                <?php if ($login::userIsAtLeast($roles::$editor)) { ?>
                                                                    &nbsp;&nbsp;&nbsp;
                                                                    <ul class="dropdown-menu">
                                                                        <li class="nav-header"><?=$this->__("subtitles.edit"); ?></li>
                                                                        <li><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/editCanvasItem/<?php echo $row["id"];?>"
                                                                               class="<?=$canvasName ?>CanvasModal"
                                                                               data="item_<?php echo $row["id"];?>"> <?=$this->__("links.edit_canvas_item"); ?></a></li>
                                                                        <li><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/delCanvasItem/<?php echo $row["id"]; ?>"
                                                                               class="delete <?=$canvasName ?>CanvasModal"
                                                                               data="item_<?php echo $row["id"];?>"> <?=$this->__("links.delete_canvas_item"); ?></a></li>
                                                                    </ul>
                                                                <?php } ?>
                                                            </div>

                                                            <h4><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/editCanvasItem/<?=$row["id"];?>"
                                                                   class="<?=$canvasName ?>CanvasModal"
                                                                   data="item_<?=$row['id'] ?>"><?php $this->e($row["description"]);?></a></h4>
                                                            <br />
                                                            <?=$this->escape($row["assumptions"]) ?>
                                                            <br />

                                                            <?php
                                                            if ($row["conclusion"] != 0 && is_numeric($row["data"]) && is_numeric($row["conclusion"])) {
                                                                $percentDone = round($row["data"] / $row["conclusion"] * 100, 2);
                                                            } else {
                                                                $percentDone = 0;
                                                            }

                                                            ?>


                                                            <div class="row" style="padding-bottom:0px;">

                                                                <div class="col-md-4">
                                                                    <small><?=$this->__('label.current') ?>: <?=$row["data"] ?></small>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <small><?=sprintf($this->__("text.percent_complete"), $percentDone); ?></small>
                                                                </div>
                                                                <div class="col-md-4" style="text-align:right">
                                                                    <small><?=$this->__('label.goal') ?>: <?=$row["conclusion"] ?></small>
                                                                </div>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $percentDone; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentDone; ?>%">
                                                                    <span class="sr-only"><?=sprintf($this->__("text.percent_complete"), $percentDone)?></span>
                                                                </div>
                                                            </div>

                                                            <div class="clearfix" style="padding-bottom: 8px;"></div>

                                                            <?php if (!empty($statusLabels)) { ?>
                                                                <div class="dropdown ticketDropdown statusDropdown colorized show firstDropdown">
                                                                    <a class="dropdown-toggle f-left status label-<?=$statusLabels[$row['status']]['dropdown'] ?>"
                                                                       href="javascript:void(0);" role="button"
                                                                       id="statusDropdownMenuLink<?=$row['id']?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <span class="text"><?=$statusLabels[$row['status']]['title'] ?></span> <i class="fa fa-caret-down"
                                                                                                                                                  aria-hidden="true"></i>
                                                                    </a>
                                                                    <ul class="dropdown-menu" aria-labelledby="statusDropdownMenuLink<?=$row['id']?>">
                                                                        <li class="nav-header border"><?=$this->__("dropdown.choose_status")?></li>
                                                                        <?php foreach ($statusLabels as $key => $data) { ?>
                                                                            <?php if ($data['active'] || true) { ?>
                                                                                <li class='dropdown-item'>
                                                                                    <a href="javascript:void(0);" class="label-<?=$data['dropdown'] ?>"
                                                                                       data-label='<?=$data["title"] ?>' data-value="<?=$row['id'] . "/" . $key ?>"
                                                                                       id="ticketStatusChange<?=$row['id'] . $key ?>"><?=$data['title'] ?></a>
                                                                                </li>
                                                                            <?php } ?>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            <?php } ?>

                                                            <?php if (!empty($relatesLabels)) {  ?>
                                                                <div class="dropdown ticketDropdown relatesDropdown colorized show firstDropdown">
                                                                    <a class="dropdown-toggle f-left relates label-<?=$relatesLabels[$row['relates']]['dropdown'] ?>"
                                                                       href="javascript:void(0);" role="button"
                                                                       id="relatesDropdownMenuLink<?=$row['id']?>" data-toggle="dropdown" aria-haspopup="true"
                                                                       aria-expanded="false">
                                                                        <span class="text"><?=$relatesLabels[$row['relates']]['title'] ?></span> <i class="fa fa-caret-down"
                                                                                                                                                    aria-hidden="true"></i>
                                                                    </a>
                                                                    <ul class="dropdown-menu" aria-labelledby="relatesDropdownMenuLink<?=$row['id']?>">
                                                                        <li class="nav-header border"><?=$this->__("dropdown.choose_relates")?></li>
                                                                        <?php foreach ($relatesLabels as $key => $data) { ?>
                                                                            <?php if ($data['active'] || true) { ?>
                                                                                <li class='dropdown-item'>
                                                                                    <a href="javascript:void(0);" class="label-<?=$data['dropdown'] ?>"
                                                                                       data-label='<?=$data["title"] ?>'
                                                                                       data-value="<?=$row['id'] . "/" . $key ?>"
                                                                                       id="ticketRelatesChange<?=$row['id'] . $key ?>"><?=$data['title'] ?></a>
                                                                                </li>
                                                                            <?php } ?>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            <?php } ?>


                                                            <div class="dropdown ticketDropdown userDropdown noBg show right lastDropdown dropRight">
                                                                <a class="dropdown-toggle f-left" href="javascript:void(0);" role="button" id="userDropdownMenuLink<?=$row['id']?>"
                                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span class="text">
                                                                        <?php if ($row["authorFirstname"] != "") {
                                                                            echo "<span id='userImage" . $row['id'] . "'>" .
                                                                                "<img src='" . BASE_URL . "/api/users?profileImage=" . $row['author'] . "' width='25' " .
                                                                                "style='vertical-align: middle;'/></span><span id='user" . $row['id'] . "'></span>";
                                                                        } else {
                                                                            echo "<span id='userImage" . $row['id'] . "'><img src='" . BASE_URL .
                                                                            "/api/users?profileImage=false' width='25' " .
                                                                            "style='vertical-align: middle;'/></span><span id='user" . $row['id'] . "'></span>";
                                                                        } ?>
                                                                    </span>
                                                                </a>
                                                                <ul class="dropdown-menu" aria-labelledby="userDropdownMenuLink<?=$row['id']?>">
                                                                    <li class="nav-header border"><?=$this->__("dropdown.choose_user")?></li>
                                                                        <?php foreach ($this->get('users') as $user) {
                                                                            echo "<li class='dropdown-item'>" .
                                                                            "<a href='javascript:void(0);' data-label='" .
                                                                            sprintf(
                                                                                $this->__("text.full_name"),
                                                                                $this->escape($user["firstname"]),
                                                                                $this->escape($user['lastname'])
                                                                            ) . "' data-value='" . $row['id'] . "_" . $user['id'] . "_" .
                                                                            $user['profileId'] . "' id='userStatusChange" . $row['id'] . $user['id'] . "' ><img src='" .
                                                                            BASE_URL . "/api/users?profileImage=" . $user['id'] . "' width='25' " .
                                                                            "style='vertical-align: middle; margin-right:5px;'/>" .
                                                                            sprintf(
                                                                                $this->__("text.full_name"),
                                                                                $this->escape($user["firstname"]),
                                                                                $this->escape($user['lastname'])
                                                                            ) . "</a>";
                                                                            echo"</li>";
                                                                        }?>
                                                                </ul>
                                                            </div>

                                                            <div class="right" style="margin-right:10px;">
                                                                <a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/editCanvasComment/<?=$row['id'] ?>"
                                                                   class="<?=$canvasName ?>CanvasModal" data="item_<?=$row['id'] ?>"
                                                                        <?php echo $nbcomments == 0 ? 'style="color: grey;"' : ''
                                                                        ?>><span class="fas fa-comments"></span></a> <small><?=$nbcomments ?></small>
                                                            </div>

                                                        </div>
                                                    </div>

                                                        <?php if ($row['milestoneHeadline'] != '') {?>
                                                        <hr style="margin-top: 5px; margin-bottom: 5px;"/><small>
                                                            <div class="row">
                                                                <div class="col-md-5" >
                                                                    <?php strlen($row['milestoneHeadline']) > 60 ?
                                                                        $this->e(substr(($row['milestoneHeadline']), 0, 60) . " ...") :  $this->e($row['milestoneHeadline']); ?>
                                                                </div>
                                                                <div class="col-md-7" style="text-align:right">
                                                                    <?=sprintf($this->__("text.percent_complete"), $row['percentDone'])?>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="progress">
                                                                        <div class="progress-bar progress-bar-success" role="progressbar"
                                                                             aria-valuenow="<?php echo $row['percentDone']; ?>" aria-valuemin="0" aria-valuemax="100"
                                                                             style="width: <?php echo $row['percentDone']; ?>%">
                                                                            <span class="sr-only"><?=sprintf($this->__("text.percent_complete"), $row['percentDone'])?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div></small>
                                                        <?php } ?>
                                                </div>
                                                </div>
                                                <?php } ?>
                                            <?php } ?>

                                        </div>
                                        <br />



                                    </div>
                                </div>
                            </div>

                    </div>
               <?php } ?>

            </div>
        <?php } ?>







<?php /*


        <div class="row">
            <div class="col-md-3">

                <?php if ($login::userIsAtLeast($roles::$editor) && count($canvasTypes) == 1 && count($allCanvas) > 0) { ?>
                    <a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/editCanvasItem?type=<?php echo $elementName; ?>"
                       class="<?=$canvasName ?>CanvasModal btn btn-primary" id="<?php echo $elementName; ?>"><?=$this->__('links.add_new_canvas_item' . $canvasName) ?></a>
                <?php } ?>

            </div>

            <div class="col-md-6 center">

            </div>

            <div class="col-md-3">
                <div class="pull-right">
                    <div class="btn-group viewDropDown">
                        <?php if (count($allCanvas) > 0 && !empty($statusLabels)) {?>
                            <?php if ($filter['status'] == 'all') { ?>
                                <button class="btn dropdown-toggle" data-toggle="dropdown"><i class="fas fa-filter"></i> <?=$this->__("status.all") ?> <?=$this->__("links.view") ?></button>
                            <?php } else { ?>
                                <button class="btn dropdown-toggle" data-toggle="dropdown"><i class="fas fa-fw <?=$this->__($statusLabels[$filter['status']]['icon']) ?>"></i> <?=$statusLabels[$filter['status']]['title'] ?> <?=$this->__("links.view") ?></button>
                            <?php } ?>
                            <ul class="dropdown-menu">
                                <li><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/showCanvas?filter_status=all" <?php if ($filter['status'] == 'all') {
                                    ?>class="active" <?php
                                    } ?>><i class="fas fa-globe"></i> <?=$this->__("status.all") ?></a></li>
                                <?php foreach ($statusLabels as $key => $data) { ?>
                                    <li><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/showCanvas?filter_status=<?=$key ?>" <?php if ($filter['status'] == $key) {
                                        ?>class="active" <?php
                                        } ?>><i class="fas fa-fw <?=$data['icon'] ?>"></i> <?=$data['title'] ?></a></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>

                    <div class="btn-group viewDropDown">
                        <?php if (count($allCanvas) > 0 && !empty($relatesLabels)) {?>
                            <?php if ($filter['relates'] == 'all') { ?>
                                <button class="btn dropdown-toggle" data-toggle="dropdown"><i class="fas fa-fw fa-globe"></i> <?=$this->__("relates.all") ?> <?=$this->__("links.view") ?></button>
                            <?php } else { ?>
                                <button class="btn dropdown-toggle" data-toggle="dropdown"><i class="fas fa-fw <?=$this->__($relatesLabels[$filter['relates']]['icon']) ?>"></i> <?=$relatesLabels[$filter['relates']]['title'] ?> <?=$this->__("links.view") ?></button>
                            <?php } ?>
                            <ul class="dropdown-menu">
                                <li><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/showCanvas?filter_relates=all" <?php if ($filter['relates'] == 'all') {
                                    ?>class="active" <?php
                                    } ?>><i class="fas fa-globe"></i> <?=$this->__("relates.all") ?></a></li>
                                <?php foreach ($relatesLabels as $key => $data) { ?>
                                    <li><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/showCanvas?filter_relates=<?=$key ?>" <?php if ($filter['relates'] == $key) {
                                        ?>class="active" <?php
                                        } ?>><i class="fas fa-fw <?=$data['icon'] ?>"></i> <?=$data['title'] ?></a></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>

                </div>
            </div>

        </div>

        <div class="clearfix"></div>




















    <?php if (count($this->get('allCanvas')) > 0) { ?>
        <div id="sortableCanvasKanban" class="sortableTicketList disabled" style="padding-top:15px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <?php foreach ($canvasItems as $row) {
                            $filterStatus = $filter['status'] ?? 'all';
                            $filterRelates = $filter['relates'] ?? 'all';

                            if (
                                $row['box'] === $elementName && ($filterStatus == 'all' ||
                                $filterStatus == $row['status']) && ($filterRelates == 'all' ||
                                $filterRelates == $row['relates'])
                            ) {
                                $comments = new \leantime\domain\repositories\comments();
                                $nbcomments = $comments->countComments(moduleId: $row['id']);
                                ?>
                            <div class="col-md-4">
                                <div class="ticketBox" id="item_<?php echo $row["id"];?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="inlineDropDownContainer" style="float:right;">

                                            <?php if ($login::userIsAtLeast($roles::$editor)) { ?>
                                                <a href="javascript:void(0)" class="dropdown-toggle ticketDropDown" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </a>
                                            <?php } ?>


                                            <?php if ($login::userIsAtLeast($roles::$editor)) { ?>
                                                &nbsp;&nbsp;&nbsp;
                                                <ul class="dropdown-menu">
                                                    <li class="nav-header"><?=$this->__("subtitles.edit"); ?></li>
                                                    <li><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/editCanvasItem/<?php echo $row["id"];?>"
                                                           class="<?=$canvasName ?>CanvasModal"
                                                           data="item_<?php echo $row["id"];?>"> <?=$this->__("links.edit_canvas_item"); ?></a></li>
                                                    <li><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/delCanvasItem/<?php echo $row["id"]; ?>"
                                                           class="delete <?=$canvasName ?>CanvasModal"
                                                           data="item_<?php echo $row["id"];?>"> <?=$this->__("links.delete_canvas_item"); ?></a></li>
                                                </ul>
                                            <?php } ?>
                                        </div>

                                        <h4><a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/editCanvasItem/<?=$row["id"];?>"
                                               class="<?=$canvasName ?>CanvasModal"
                                               data="item_<?=$row['id'] ?>"><?php $this->e($row["description"]);?></a></h4>
                                        <br />
                                        <?=$this->escape($row["assumptions"]) ?>
                                        <br />

                                        <?php
                                        if ($row["conclusion"] != 0 && is_numeric($row["data"]) && is_numeric($row["conclusion"])) {
                                            $percentDone = round($row["data"] / $row["conclusion"] * 100, 2);
                                        } else {
                                            $percentDone = 0;
                                        }

                                        ?>


                                        <div class="row" style="padding-bottom:0px;">

                                            <div class="col-md-4">
                                                <small><?=$this->__('label.current') ?>: <?=$row["data"] ?></small>
                                            </div>
                                            <div class="col-md-4">
                                                <small><?=sprintf($this->__("text.percent_complete"), $percentDone); ?></small>
                                            </div>
                                            <div class="col-md-4" style="text-align:right">
                                                <small><?=$this->__('label.goal') ?>: <?=$row["conclusion"] ?></small>
                                            </div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $percentDone; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentDone; ?>%">
                                                <span class="sr-only"><?=sprintf($this->__("text.percent_complete"), $percentDone)?></span>
                                            </div>
                                        </div>

                                        <div class="clearfix" style="padding-bottom: 8px;"></div>

                                        <?php if (!empty($statusLabels)) { ?>
                                            <div class="dropdown ticketDropdown statusDropdown colorized show firstDropdown">
                                                <a class="dropdown-toggle f-left status label-<?=$statusLabels[$row['status']]['dropdown'] ?>"
                                                   href="javascript:void(0);" role="button"
                                                   id="statusDropdownMenuLink<?=$row['id']?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text"><?=$statusLabels[$row['status']]['title'] ?></span> <i class="fa fa-caret-down"
                                                                                                                              aria-hidden="true"></i>
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="statusDropdownMenuLink<?=$row['id']?>">
                                                    <li class="nav-header border"><?=$this->__("dropdown.choose_status")?></li>
                                                    <?php foreach ($statusLabels as $key => $data) { ?>
                                                        <?php if ($data['active'] || true) { ?>
                                                            <li class='dropdown-item'>
                                                                <a href="javascript:void(0);" class="label-<?=$data['dropdown'] ?>"
                                                                   data-label='<?=$data["title"] ?>' data-value="<?=$row['id'] . "/" . $key ?>"
                                                                   id="ticketStatusChange<?=$row['id'] . $key ?>"><?=$data['title'] ?></a>
                                                            </li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>

                                        <?php if (!empty($relatesLabels)) {  ?>
                                            <div class="dropdown ticketDropdown relatesDropdown colorized show firstDropdown">
                                                <a class="dropdown-toggle f-left relates label-<?=$relatesLabels[$row['relates']]['dropdown'] ?>"
                                                   href="javascript:void(0);" role="button"
                                                   id="relatesDropdownMenuLink<?=$row['id']?>" data-toggle="dropdown" aria-haspopup="true"
                                                   aria-expanded="false">
                                                    <span class="text"><?=$relatesLabels[$row['relates']]['title'] ?></span> <i class="fa fa-caret-down"
                                                                                                                                aria-hidden="true"></i>
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="relatesDropdownMenuLink<?=$row['id']?>">
                                                    <li class="nav-header border"><?=$this->__("dropdown.choose_relates")?></li>
                                                    <?php foreach ($relatesLabels as $key => $data) { ?>
                                                        <?php if ($data['active'] || true) { ?>
                                                            <li class='dropdown-item'>
                                                                <a href="javascript:void(0);" class="label-<?=$data['dropdown'] ?>"
                                                                   data-label='<?=$data["title"] ?>'
                                                                   data-value="<?=$row['id'] . "/" . $key ?>"
                                                                   id="ticketRelatesChange<?=$row['id'] . $key ?>"><?=$data['title'] ?></a>
                                                            </li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>


                                        <div class="dropdown ticketDropdown userDropdown noBg show right lastDropdown dropRight">
                                            <a class="dropdown-toggle f-left" href="javascript:void(0);" role="button" id="userDropdownMenuLink<?=$row['id']?>"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text">
                                                    <?php if ($row["authorFirstname"] != "") {
                                                        echo "<span id='userImage" . $row['id'] . "'>" .
                                                            "<img src='" . BASE_URL . "/api/users?profileImage=" . $row['author'] . "' width='25' " .
                                                            "style='vertical-align: middle;'/></span><span id='user" . $row['id'] . "'></span>";
                                                    } else {
                                                        echo "<span id='userImage" . $row['id'] . "'><img src='" . BASE_URL .
                                                        "/api/users?profileImage=false' width='25' " .
                                                        "style='vertical-align: middle;'/></span><span id='user" . $row['id'] . "'></span>";
                                                    } ?>
                                                </span>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="userDropdownMenuLink<?=$row['id']?>">
                                                <li class="nav-header border"><?=$this->__("dropdown.choose_user")?></li>
                                                    <?php foreach ($this->get('users') as $user) {
                                                        echo "<li class='dropdown-item'>" .
                                                        "<a href='javascript:void(0);' data-label='" .
                                                        sprintf(
                                                            $this->__("text.full_name"),
                                                            $this->escape($user["firstname"]),
                                                            $this->escape($user['lastname'])
                                                        ) . "' data-value='" . $row['id'] . "_" . $user['id'] . "_" .
                                                        $user['profileId'] . "' id='userStatusChange" . $row['id'] . $user['id'] . "' ><img src='" .
                                                        BASE_URL . "/api/users?profileImage=" . $user['id'] . "' width='25' " .
                                                        "style='vertical-align: middle; margin-right:5px;'/>" .
                                                        sprintf(
                                                            $this->__("text.full_name"),
                                                            $this->escape($user["firstname"]),
                                                            $this->escape($user['lastname'])
                                                        ) . "</a>";
                                                        echo"</li>";
                                                    }?>
                                            </ul>
                                        </div>

                                        <div class="right" style="margin-right:10px;">
                                            <a href="<?=BASE_URL ?>/<?=$canvasName ?>canvas/editCanvasComment/<?=$row['id'] ?>"
                                               class="<?=$canvasName ?>CanvasModal" data="item_<?=$row['id'] ?>"
                                                    <?php echo $nbcomments == 0 ? 'style="color: grey;"' : ''
                                                    ?>><span class="fas fa-comments"></span></a> <small><?=$nbcomments ?></small>
                                        </div>

                                    </div>
                                </div>

                                    <?php if ($row['milestoneHeadline'] != '') {?>
                                    <hr style="margin-top: 5px; margin-bottom: 5px;"/><small>
                                        <div class="row">
                                            <div class="col-md-5" >
                                                <?php strlen($row['milestoneHeadline']) > 60 ?
                                                    $this->e(substr(($row['milestoneHeadline']), 0, 60) . " ...") :  $this->e($row['milestoneHeadline']); ?>
                                            </div>
                                            <div class="col-md-7" style="text-align:right">
                                                <?=sprintf($this->__("text.percent_complete"), $row['percentDone'])?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" role="progressbar"
                                                         aria-valuenow="<?php echo $row['percentDone']; ?>" aria-valuemin="0" aria-valuemax="100"
                                                         style="width: <?php echo $row['percentDone']; ?>%">
                                                        <span class="sr-only"><?=sprintf($this->__("text.percent_complete"), $row['percentDone'])?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div></small>
                                    <?php } ?>
                            </div>
                            </div>
                            <?php } ?>
                        <?php } ?>

                    </div>
                    <br />



                </div>
            </div>
        </div>

        <?php if (count($canvasItems) == 0) {
            echo "<br /><br /><div class='center'>";

                echo "<div class='svgContainer'>";
                    echo file_get_contents(ROOT . "/images/svg/undraw_design_data_khdb.svg");
                    echo "</div>";

                echo"<h3>" . $this->__("headlines.goal.analysis") . "</h3>";
                echo "<br />" . $this->__("text.goal.helper_content");


                echo"</div>";
         } ?>


        <div class="clearfix"></div>
    <?php } ?>

 */

require($this->getTemplatePath('canvas', 'showCanvasBottom.inc.php')); ?>
