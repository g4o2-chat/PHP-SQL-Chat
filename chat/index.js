class Message {
    constructor(message, message_date, user_id, username, pfp) {
        this.message = message;
        this.message_date = message_date;
        this.user_id = user_id;
        this.username = username;
        this.pfp = pfp;
        this.localDate = new Date(this.message_date.toLocaleString())
    }
    appendMessage() {
        const div = document.createElement("div");
        div.classList.add("message-container");

        const img = document.createElement("img");
        img.classList.add("message-sender-pfp");
        img.setAttribute("src", this.pfp);
        img.setAttribute("alt", `Profile picture for ${this.username}`);
        img.setAttribute("id", this.user_id);

        const messageContentDiv = document.createElement("div");
        messageContentDiv.classList.add("message-content");

        const messageHeaderDiv = document.createElement("div");
        messageHeaderDiv.classList.add("message-header");

        const messageSenderSpan = document.createElement("span");
        messageSenderSpan.classList.add("message-sender");
        messageSenderSpan.innerText = this.username.concat(" ");
        
        const messageDateTime = document.createElement("time");
        messageDateTime.classList.add("message-datetime");
        messageDateTime.setAttribute("datetime", this.localDate);
        messageDateTime.innerText = this.localDate;

        messageHeaderDiv.appendChild(messageSenderSpan);
        messageHeaderDiv.appendChild(messageDateTime);

        const messageBodyDiv = document.createElement("div");
        messageBodyDiv.classList.add("message-body");

        const messageSpan = document.createElement("span");
        messageSpan.classList.add("message");
        messageSpan.innerText = escapeHtml(this.message);

        messageBodyDiv.appendChild(messageSpan);

        messageContentDiv.appendChild(messageHeaderDiv);
        messageContentDiv.appendChild(messageBodyDiv);

        div.appendChild(img);
        div.appendChild(messageContentDiv);

        document.getElementById('messages').appendChild(div);
    }
}

class User {
    constructor(username, pfp, status) {
        this.username = username;
        this.pfp = pfp;
        this.status = status;
    }
    addUserToUsers() {
        const liElement = document.createElement("li");

        const imgElement = document.createElement("img");
        if(this.pfp) {
            imgElement.setAttribute("src", this.pfp);
        } else {
            imgElement.setAttribute("src", "../assets/images/default-user-square.png");
        }
        imgElement.setAttribute("alt", `Profile picture for ${this.username}`);

        const spanElement = document.createElement("span");
        spanElement.textContent = this.username;

        liElement.appendChild(imgElement);
        liElement.appendChild(spanElement);

        document.getElementById("users").appendChild(liElement);
    }
    setOnline() {
        const pElement = document.createElement("p");
        pElement.textContent = `User ${this.username} connected`;
        document.getElementById("messages").appendChild(pElement);
    }
    setOffline() {
        const pElement = document.createElement("p");
        pElement.textContent = `User ${this.username} disconnected`;
        document.getElementById("messages").appendChild(pElement);
    }
}