class AssignHeader extends HTMLElement {
  constructor() {
    super();
  }

  connectedCallback() {
    this.innerHTML =
      `<header>
        <div class="container">
          <div class="title">
            <a href="../." id="home-button">GTLugo</a>
          </div>
          <nav>
            <ul>
              <li><a href="../login.html" class="nav-button">LOGIN</a></li>
              <!--<li>|</li>-->
              <!--<li><a href="../register.html" class="nav-button">REGISTER</a></li>-->
            </ul>
          </nav>
        </div>
      </header>`;

  }
}

customElements.define('assign-header', AssignHeader);