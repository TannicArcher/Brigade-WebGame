async function sendForm(e)
{
    e.preventDefault();
    let response = await fetch('/chat/send', {
        method: 'POST',
        body: new FormData(form)
    });

    let result = await response.json()

    let results = document.querySelector('#results');
    let textarea = document.querySelector('#text');
    results.classList.toggle('show')
    results.innerHTML = result.message
    if (result.type === 'success') textarea.value = ''
    setTimeout(() => results.classList.toggle('show'), 3000)
    await loadChat()
}

async function loadChat() {
    let page = window.location.search
    const data = { page: !!page ? page : '?page=1' }

    let response = await fetch('/chat/load' + page, {
        method: 'POST',
        body: JSON.stringify(data)
    });

    document.getElementById('messages').innerHTML = await response.text()
}

function bb(tag) {
    let Field = document.querySelector('#text');
    Field.value += tag;
}
