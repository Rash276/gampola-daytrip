<?php
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
verify_csrf(); $id=(int)($_POST['place_id']??0); $action=(string)($_POST['action']??'add');
if (!place_by_id($id)) { flash('That place is no longer available.','error'); header('Location: index.php'); exit; }
$_SESSION['plan'] = array_values(array_unique(array_map('intval',$_SESSION['plan']??[])));
if ($action==='remove') { $_SESSION['plan']=array_values(array_diff($_SESSION['plan'],[$id])); flash('Place removed from your day plan.'); }
elseif (!in_array($id,$_SESSION['plan'],true)) { $_SESSION['plan'][]=$id; flash('Added to your day plan.'); }
$return=(string)($_POST['return']??'planner.php'); if (!preg_match('/^(index\.php|planner\.php|place\.php\?id=\d+)$/',$return)) $return='planner.php';
header('Location: '.$return); exit;
