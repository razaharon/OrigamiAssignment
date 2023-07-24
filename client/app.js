const apiUrl = 'http://localhost:8080';
const tasksContainer = document.getElementById('task-container');
const tasks = new Tasks();
window.addEventListener('load', () => renderTasks());

function renderTasks() {
    tasks.items.forEach(task => tasksContainer.appendChild(getTaskElement(task)));
}

function getTaskElement(task) {
    const [div, button, title, description] = ['div', 'button', 'h3', 'p'].map(e => document.createElement(e));

    div.id = 'task-' + task.id;
    div.classList.add('task');

    button.classList.add('close-btn');
    button.onclick = () => deleteTask(task.id);
    button.innerHTML = '&times;';

    title.innerText = task.title;
    description.innerText = task.description;
    div.append(button, title, description)

    return div;
}

function addTask() {
    const title = document.getElementById('title').value.trim();
    const description = document.getElementById('description').value.trim();
    if (!title || !description) {
        return;
    }
    const task = tasks.add(title, description);
    postTask(task);
    tasksContainer.appendChild(getTaskElement(task));
    document.getElementById('taskForm').reset();
}

function deleteTask(id) {
    tasks.remove(id);
    document.getElementById('task-' + id).remove();
}

async function postTask(task) {
    const response = await fetch(apiUrl + '/task', {
        method: 'POST',
        body: JSON.stringify(task)
    });
    const data = await response.json();
    if (!data?.error) {
        return data;
    }
    return null;
}