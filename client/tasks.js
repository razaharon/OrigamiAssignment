class Tasks {

    _lastId = 0;
    items = [];

    constructor() {
        this.items = this._getFromStorage();
        if (this.items.length) {
            this._lastId = this.items[this.items.length - 1].id;
        }
    }

    add(title, description) {
        if (!title || !description) {
            return null;
        }
        const task = { id: ++this._lastId, title, description, date: new Date() };
        this.items.push(task);
        this._saveToStorage();
        return task;
    }

    remove(id) {
        const index = this.items.findIndex(task => task.id === id);
        if (index < 0) {
            return null;
        } 
        this.items.splice(index, 1);
        this._saveToStorage();
    }

    _getFromStorage() {
        return JSON.parse(localStorage.getItem('tasks') || '[]');
    }

    _saveToStorage() {
        localStorage.setItem('tasks', JSON.stringify(this.items));
    }
}