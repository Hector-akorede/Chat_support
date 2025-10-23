<?php
// include session initializer but silence any accidental output (session_id echo etc.)
ob_start();
include __DIR__ . '/../src/session_init.php';
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SupportBot - 24/7 Customer Service Assistant</title>
      <!-- Place at the bottom of your site's HTML (e.g. footer) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

<div class="bg-gray-50 text-gray-800 min-h-screen flex flex-col items-center">

    <header class="w-full max-w-3xl mt-12 px-6">
    <h1 class="text-3xl font-extrabold">SupportBot</h1>
    <p class="text-sm text-gray-500">Created by Akorede</p>
  </header>

  <main class="w-full max-w-3xl mt-8 px-6">
    <section class="bg-white rounded-xl shadow p-6 border border-gray-100 shadow-xl shadow-gray-400">
      <h2 class="text-xl font-semibold mb-2">Quick intro</h2>
      <p class="text-gray-600 mb-4">SupportBot is a lightweight customer support chat widget that captures conversations for your team and helps route complex issues to human agents.</p>

      <h3 class="text-lg font-medium mt-4 mb-3">Key features</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="p-3 bg-gray-50 rounded-md border">
          <strong class="block">24/7 Availability</strong>
          <span class="text-sm text-gray-500">Instant replies to common questions.</span>
        </div>
        <div class="p-3 bg-gray-50 rounded-md border">
          <strong class="block">Conversation History</strong>
          <span class="text-sm text-gray-500">Messages are stored per session for review.</span>
        </div>
        <div class="p-3 bg-gray-50 rounded-md border">
          <strong class="block">Human Handoff</strong>
          <span class="text-sm text-gray-500">Escalate chats to agents when needed.</span>
        </div>
      </div>

      <p class="text-xs text-gray-400 mt-4">Tip: open the chat using the button at bottom-right to test the widget.</p>
    </section>
  </main>
</div>
  
<div class="chatbot">


<style>
#chatbot-toggle {
  position: fixed; bottom: 20px; right: 20px;
  background-color: rgb(220, 38, 38); color: #fff; border: none; border-radius: 50%;
  width: 58px; height: 58px; font-size: 2rem; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000;
}
#chatbot-widget {
  position: fixed; bottom: 90px; right: 16px;
  width: 96vw; max-width: 390px; height: 72vh;
  background: #fff; box-shadow: 0 8px 24px rgba(0,0,0,0.18);
  border-radius: 16px; overflow: hidden;
  display: flex; /* Shown/hidden by JS */
  flex-direction: column;
  justify-content: space-between;
  display: none;   /* Make flex container */
}
.chatbot-header {
   background-color: rgb(220, 38, 38); color: #fff; padding: 16px; font-weight: 600; font-size: 18px; text-align: center;
  height: 70px; /* Fixed height for header */
}
.chatbot-body {
  padding: 16px; overflow-y: auto; background: #f7f8fa; display: flex; flex-direction: column; gap: 10px;
    /* Fills available space */
  height: calc(100vh - 70px - 70px); /* Prevents overflow issues */
  /* height: 70%; <-- REMOVE this line */
}
.bot-message {
  align-self: flex-start; background: #e6e6e6; color: #222; padding: 11px 16px; border-radius: 18px; font-size: 15px; max-width: 80%; word-break: break-word;
}
.user-message {
  align-self: flex-end;  background-color: rgb(220, 38, 38);; color: #fff; padding: 11px 16px; border-radius: 18px; font-size: 15px; max-width: 80%; word-break: break-word;
}
.chatbot-input {
  display: flex; border-top: 1px solid #eee; background: #fff; padding: 10px 12px;
   box-shadow: 0 -2px 4px rgba(0,0,0,0.1); height: 70px; /* Fixed height for input area */
}
.chatbot-input input {
  flex: 1; border: 1px solid #ccc; border-radius: 18px; padding: 10px 16px; font-size: 15px; outline: none;
}
.chatbot-input button {
   background-color: rgb(220, 38, 38); color: #fff; border: none; border-radius: 18px; padding: 10px 16px; margin-left: 8px; cursor: pointer; font-size: 15px; transition: background 0.2s;
}
.chatbot-input button:hover { background-color: rgba(163, 26, 26, 1); }
@media (max-width: 500px) {
  #chatbot-widget {
    width: 80vw; right: 20px;  max-width: none; max-height: 70vh; border-radius: 10px; bottom: 100px; 
  }
  #chatbot-toggle { right: 12px; bottom: 60px; }
  .chatbot-body { padding: 9px; }
  .chatbot-header { font-size: 16px; padding: 12px; }

}
.chatbot-input input {
   padding: 10px 10px; font-size: 15px; outline: none;
}
</style>

<button id="chatbot-toggle"><i class="ri-chat-3-line"></i></button>
<div id="chatbot-widget">
  <div class="chatbot-header">Welcome to SupportBot</div>
  <div class="chatbot-body" id="chatbot-body">
    <div class="bot-message">
      <p><strong>Welcome to SupportBot</strong></p>
      <p>Hi — tell me your name and how I can help.</p>
    </div>
  </div>
  <div class="chatbot-input">
    <input type="text" id="chatbot-input-msg" autocomplete="off" placeholder="Type your message..." />
    <button id="chatbot-send"><i class="ri-send-plane-2-fill"></i></button>
  </div>
</div>


<script>
let botSessionId = localStorage.getItem("bot_session_id") || "";
function startBotSession() {
  if (!botSessionId) {
    $.get("session_init.php", function(res) {
      botSessionId = (typeof res === "string" ? JSON.parse(res) : res).session_id;
      localStorage.setItem("bot_session_id", botSessionId);
      loadBotMessages();
    });
  } else {
    loadBotMessages();
  }
  setInterval(loadBotMessages, 4000);
}
function sendBotMessage() {
  const msg = $('#chatbot-input-msg').val().trim();
  if (!msg || !botSessionId) return;
  $.post("send_message.php", { session_id: botSessionId, message: msg, sender: "user" }, function(){
    $('#chatbot-input-msg').val('');
    loadBotMessages();
  });
}
function loadBotMessages() {
  if (!botSessionId) return;
  $.get("fetch_messages.php?session_id=" + encodeURIComponent(botSessionId), function(data) {
    const messages = typeof data === "string" ? JSON.parse(data) : data;
    const $body = $('#chatbot-body');
    $body.html(`
  <div class="bot-message">
     <p><strong>Welcome to SupportBot</strong></p>
      <p>Hi — tell me your name and how I can help.</p>
  </div>
`);

    messages.forEach(msg => {
      const cls = msg.sender === 'user' ? 'user-message' : 'bot-message';
      $body.append(`<div class="${cls}">${msg.message}</div>`);
    });
    $body.scrollTop($body[0].scrollHeight);
  });
}
$('#chatbot-toggle').on('click', function () {
    const $widget = $('#chatbot-widget');
    if ($widget.is(':visible')) {
        $widget.fadeOut();
    } else {
        $widget
            .css('display', 'flex')   // always use flex when showing
            .hide()
            .fadeIn();
    }
});
$('#chatbot-send').on('click', sendBotMessage);
$('#chatbot-input-msg').on('keypress', function (e) { if (e.which === 13) sendBotMessage(); });
$(document).ready(startBotSession);
function appendMessage(msg) {
  const container = document.getElementById('chat-messages');
  const wrap = document.createElement('div');
  wrap.className = 'msg ' + (msg.sender || 'user');
  const text = document.createElement('div');
  text.textContent = msg.text; // safe: avoid innerHTML
  wrap.appendChild(text);
  container.appendChild(wrap);
}
// AJAX calls — ensure URLs point to local public files
// e.g. fetch('/Chatbot/public/fetch_messages.php?session_id=' + sid)
</script>
</body>
</html>


