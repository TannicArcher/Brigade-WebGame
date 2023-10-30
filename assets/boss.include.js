function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
  
    tabcontent = document.getElementsByClassName("tabContent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
  
    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tabLinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
  
    // Show the current tab, and add an "active" class to the link that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}
document.getElementById("defaultOpen").click();

async function loadChat(id) {
  let fd = new FormData();
  fd.append("idChat", id)
  fd.append("page", page: !!page ? page : '?page=1')

  let response = await fetch('/boss/chatLoad', {
      method: 'POST',
      body: fd
  });

  document.getElementById('messages').innerHTML = await response.text()
}