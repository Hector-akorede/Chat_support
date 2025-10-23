<?php
require __DIR__ . '/../src/db_connect.php';
?>

<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Chat Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    html, body { height: 100%; margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f5f8fa;
      margin: 0;
      display: flex;
      flex-direction: row;
      height: 100vh;
      min-height: 0;
      width: 100vw;
      min-width: 0;
      overflow: hidden;
    }
    .sidebar {
      width: 280px;
      background-color: rgb(220, 38, 38);
      color: #fff;
      display: flex;
      flex-direction: column;
      transition: left 0.3s, width 0.2s;
      position: relative;
      z-index: 1001;
      min-width: 0;
      height: 100vh;
      max-height: 100vh;
      left: 0;
    }
    .sidebar h2 {
      padding: 18px 18px 12px 18px;
      font-size: 20px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      border-bottom: 1px solid rgba(255,255,255,0.12);
    }
    .chat-list {
      flex: 1;
      overflow-y: auto;
      min-height: 0;
    }
    .chat-item {
      padding: 16px 18px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      cursor: pointer;
      position: relative;
      background: none;
      transition: background 0.2s;
    }
    .chat-item:hover, .chat-item.active { background: rgba(255,255,255,0.09); }
    .chat-item .session-id { font-weight: 500; font-size: 15px; }
    .chat-item .preview { font-size: 13px; opacity: 0.85; margin-top: 2px; }
    .chat-item .time { position: absolute; right: 18px; top: 16px; font-size: 11px; opacity:0.8; }
    .sidebar .stats {
      padding: 10px 18px;
      font-size: 12px;
      border-top: 1px solid rgba(255,255,255,0.14);
      background: rgba(0,0,0,0.04);
    }
    .main {
      flex: 1 1 0;
      background: #f5f8fa;
      display: flex;
      flex-direction: column;
      min-width: 0;
      min-height: 0;
      height: 100vh;
      max-height: 100vh;
    }
    .chat-header {
      background: #fff;
      padding: 16px 22px;
      font-weight: 600;
      font-size: 17px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.03);
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
    }
    .chat-body {
      flex: 1 1 0;
      min-height: 0;
      padding: 18px 22px;
      overflow-y: auto;
      background: #f7f8fa;
      display: flex;
      flex-direction: column;
    }
    .message {
      max-width: 72%;
      padding: 12px 15px;
      border-radius: 14px;
      margin: 12px 0;
      font-size: 15px;
      word-break: break-word;
      position: relative;
      line-height: 1.45;
      box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
    .message.admin { background-color: rgb(220, 38, 38); color: #fff; margin-left: auto; }
    .message.user { background: #fff; border: 1px solid #e3e7ed; }
    .timestamp { font-size: 11px; opacity: 0.7; margin-top: 3px; text-align: right; }
    .chat-input {
      display: flex;
      padding: 12px 22px;
      background: #fff;
      border-top: 1px solid #e3e7ed;
    }
    .chat-input input {
      flex: 1;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 7px;
      font-size: 15px;
      outline: none;
      transition: border 0.2s;
    }
    .chat-input input:focus { border-color: #1976d2; }
    .chat-input button {
      margin-left: 10px;
      background-color: rgb(220, 38, 38);
      color: #fff;
      border: none;
      padding: 10px 15px;
      border-radius: 7px;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .chat-input button:hover { background-color: rgba(163, 26, 26, 1); }
    .hamburger {
      display: none;
      position: absolute;
      left: 10px;
      top: 14px;
      background: none;
      border: none;
      color: #1976d2;
      font-size: 28px;
      z-index: 1100;
      cursor: pointer;
    }
    .sidebar-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.32);
      z-index: 1000;
    }
    @media (max-width: 900px) {
      .sidebar { width: 90px; }
      .sidebar h2 { font-size: 18px; padding: 10px; }
      .chat-item .session-id, .chat-item .preview { font-size: 13px; }
      .chat-item .preview { display: none; }
      .chat-item .time { display: none; }
      .sidebar .stats { font-size: 11px; }
    }
    @media (max-width: 650px) {
      html, body { height: 100dvh; min-height: 0; }
      body {
        flex-direction: row;
        height: 100dvh;
        min-height: 0;
        width: 100vw;
        min-width: 0;
      }
      .sidebar {
        position: fixed;
        top: 0;
        left: -100vw;
        height: 100dvh;
        width: 200px;
        min-width: 200px;
        max-width: 80vw;
        transition: left 0.3s;
        box-shadow: 2px 0 16px rgba(0,0,0,0.12);
        z-index: 1001;
      }
      .sidebar.open { left: 0; }
      .main {
        width: 100vw;
        height: 100dvh;
        max-height: 100dvh;
        min-width: 0;
        min-height: 0;
      }
      .chat-body { padding: 12px 8px; }
      .chat-header, .chat-input { padding: 10px; }
      .hamburger { display: block; }
      .sidebar-overlay { display: none; }
      .sidebar.open ~ .sidebar-overlay { display: block; }
      .sidebar .stats, .sidebar h2 { font-size: 15px; padding: 14px 10px; }
      .chat-list { max-height: calc(100dvh - 100px); }
      .chat-header { height: 40px }
      .message {
      max-width: 40%;
      padding: 12px 15px;
      border-radius: 14px;
      font-size: 12px;
    }
     .message.admin{
      margin-left: auto;
     }
    }
  </style>
</head>
<body>
  <button class="hamburger" id="sidebar-hamburger" aria-label="Open sidebar"><i class="ri-menu-line"></i></button>
  <div class="sidebar" id="sidebar">
    <h2><i class="ri-message-3-line"></i> Chats</h2>
    <div class="chat-list" id="admin-chat-list"></div>
    <div class="stats" id="admin-stats"></div>
  </div>
  <div class="sidebar-overlay" id="sidebar-overlay"></div>
  <div class="main">
    <div class="chat-header" id="admin-chat-header">Select a chat</div>
    <div class="chat-body" id="admin-chat-body"></div>
    <div class="chat-input">
      <input type="text" placeholder="Type your reply..." id="admin-msg" autocomplete="off"/>
      <button id="admin-send"><i class="ri-send-plane-2-fill"></i></button>
    </div>
  </div>



<script>
let currentSession = '';
function loadSessions() {
  $.get("../src/fetch_sessions.php", function(data){
    data = typeof data==="string"?JSON.parse(data):data;
    let html = '';
    let active = 0;
    data.forEach(row => {
      let preview = row.preview ? row.preview : '';
      let sessionId = row.session_id;
      let activeClass = (sessionId === currentSession) ? 'active' : '';
      html += `<div class="chat-item ${activeClass}" data-session="${sessionId}">
        <div class="session-id">${sessionId}</div>
        <div class="preview">${preview}</div>
        <div class="time">${row.last_msg_time?new Date(row.last_msg_time).toLocaleTimeString([],{hour:'2-digit', minute:'2-digit'}):''}</div>
      </div>`;
      active++; // show all as active for simplicity
    });
    $('#admin-chat-list').html(html);
    $('#admin-stats').html(`Active Chats: ${active}<br />Total Sessions: ${data.length}`);
    $('.chat-item').off().on('click',function(){
      let sid = $(this).data('session');
      currentSession = sid;
      $('.chat-item').removeClass('active');
      $(this).addClass('active');
      loadMessages(sid);
      // On mobile, close sidebar after selecting chat
      if(window.innerWidth <= 650) closeSidebar();
    });
  });
}
function loadMessages(sessionId) {
  $.get("../public/fetch_messages.php?session_id="+encodeURIComponent(sessionId), function(data){
    data = typeof data==="string"?JSON.parse(data):data;
    $('#admin-chat-header').html(`Chat: <strong>${sessionId}</strong>`);
    let html = '';
    data.forEach(msg=>{
      // Update to use correct property names
      html += `<div class="message ${msg.sender}">
        ${msg.message || msg.text}
        <div class="timestamp">${formatTime(msg.created_at)}</div>
      </div>`;
    });
    $('#admin-chat-body').html(html);
    $('#admin-chat-body').scrollTop($('#admin-chat-body')[0].scrollHeight);
  });
}
function sendAdminMessage() {
  const msg = $('#admin-msg').val().trim();
  if(!msg || !currentSession)return;
  $.post("../public/send_message.php", { session_id: currentSession, message: msg, sender: "admin" }, function(){
    $('#admin-msg').val('');
    loadMessages(currentSession);
  });
}
function formatTime(t) {
  if(!t)return '';
  let d = new Date(t.replace(' ','T'));
  let now = new Date();
  if(now.toDateString() === d.toDateString())
    return d.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
  return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
}

// Hamburger menu support for mobile
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');
const hamburger = document.getElementById('sidebar-hamburger');
function openSidebar() {
  sidebar.classList.add('open');
  overlay.style.display = "block";
}
function closeSidebar() {
  sidebar.classList.remove('open');
  overlay.style.display = "none";
}
hamburger.addEventListener('click', openSidebar);
overlay.addEventListener('click', closeSidebar);

$('#admin-send').click(sendAdminMessage);
$('#admin-msg').keypress(function(e){if(e.which===13)sendAdminMessage();});
setInterval(function(){ loadSessions(); if(currentSession)loadMessages(currentSession); },4000);
$(document).ready(loadSessions);
</script>
<script>
function renderMessages(list) {
  const el = document.getElementById('admin-messages');
  el.innerHTML = '';
  list.forEach(m => {
    const row = document.createElement('div');
    row.className = 'message-row';
    const who = document.createElement('div');
    who.className = 'who';
    who.textContent = m.sender;
    const body = document.createElement('div');
    body.className = 'body';
    body.textContent = m.text; // safe insertion
    row.appendChild(who);
    row.appendChild(body);
    el.appendChild(row);
  });
}
</script>
</body>
</html>