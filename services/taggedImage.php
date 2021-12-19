<?php
use toolmarr\WebSlideshow\DbWebSlideshow;
use toolmarr\WebSlideshow\DAL\EntityFactory;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

if (isset($_POST) && isset($_POST['imageID']) && isset($_POST['tagID']) && isset($_POST['operation'])) {
    $entityFactory = new EntityFactory($configuration['database']);
    $taggedImageEntity = $entityFactory->getEntity("taggedImage");
    $taggedImageEntity->imageID = $_POST['imageID'];
    $taggedImageEntity->tagID = $_POST['tagID'];

    if ($_POST['operation'] == 'adding') {
        if ($taggedImageEntity->insert()) {
            echo 'success';
        } else {
            echo 'failure - insert failed';
        }
    } else if ($_POST['operation'] == 'removing') {
        if ($taggedImageEntity->delete()) {
            echo 'success';
        } else {
            echo 'failure - delete failed';
        }
    } else {
        echo 'failure - invalid operation';
    }
} else {
    echo 'failure - invalid inputs';
}