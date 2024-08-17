<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>CV</title>
    <link
      href="https://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css"
      rel="stylesheet"
      id="bootstrap-css"
    />
    <style>
        /* heading */

h1 {
  font: bold 100% sans-serif;
  letter-spacing: 0.5em;
  text-align: center;
  text-transform: uppercase;
}

.alaa {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
}

body {
  box-sizing: border-box;
  height: 11in;
  margin: 0 auto;
  overflow: hidden;
  padding: 0.5in;
  width: 7.5in;
}
body {
  background: #fff;
  border-radius: 1px;
  box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
}

/* header */

header {
  margin: 0 0 3em;
}
header:after {
  clear: both;
  content: "";
  display: table;
}

.child {
  display: flex;
  flex-direction: column;
}

header h1 {
  background: #e40101;
  border-radius: 0.25em;
  color: #fff;
  margin: 0 0 1em;
  padding: 0.5em 0;
}
header address {
  float: left;
  font-size: 95%;
  font-style: normal;
  line-height: 1.25;
  margin: 0 1em 1em 0;
}
article address.norm h4 {
  font-size: 125%;
  font-weight: bold;
  color: red;
  display: contents;
}
address.norm ul {
    list-style: lower-greek;
    /* display: flex; */
}

article {
  /* display: grid; */
}
.fi {

}


article address.norm {
  float: left;
  font-size: 95%;
  font-style: normal;
  font-weight: normal;
  line-height: 1.25;
  margin: 0 1em 1em 0;
  display: flex;
}
header address p {
  margin: 0 0 0.25em;
}
header span,
header img {
  display: block;
  float: right;
}
header span {
  margin: 0 0 1em 1em;
  max-height: 25%;
  max-width: 60%;
  position: relative;
}
header span img {
  max-height: 100%;
  max-width: 100%;
}
header input {
  cursor: pointer;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
  height: 100%;
  left: 0;
  opacity: 0;
  position: absolute;
  top: 0;
  width: 100%;
}

/* article */

article,
article address,
article:after {
  clear: both;
  content: "";
  display: table;
}
article h1 {
  clip: rect(0 0 0 0);
  position: absolute;
}

article address {
  float: left;
  font-size: 125%;
  font-weight: bold;
}

    </style>
  </head>
  <body>
    <div id="cv">
        <header>
            <h1>CV</h1>
            <hr width="100%" color="red" size="5" />
            <address>
                <h2>Ali Mohammad</h2>
                <p>alexandercross202@gmail.com</p>
                <p>45189, Research Place, Suite 150A</p>
            </address>
            <img style="align-self: flex-end" alt="it" src="{{asset('/images/job_seeker/profilePhoto/Picture16.jpg')}}" width="120" />
            <hr width="100%" color="red" size="5" />
      </header>
      <article>
        <div class="alaa">
          <address class="norm">
            <h4>Skills</h4>
            <div class="fi">
                <ul>
                    <li>BLA BLA</li>
                    <li>BLA BLA</li>
                    <li>BLA BLA</li>
                  </ul>
            </div>
          </address>
          <hr width="100%" color="red" size="5" />
          <address class="norm">
            <h4>Certificates</h4>
            <ul>
              <li>IT</li>
              <li>BLA BLA</li>
              <li>BLA BLA BLA</li>
            </ul>
          </address>
          <hr width="100%" color="red" size="5" />
          <address class="norm">
            <h4>Languages</h4>
            <ul>
              <li>IT</li>
              <li>BLA BLA</li>
              <li>BLA BLA BLA</li>
            </ul>
          </address>
          <hr width="100%" color="red" size="5" />
          <address class="norm">
            <h4>Projects</h4>
            <ul>
              <li>IT</li>
              <li>BLA BLA</li>
              <li>BLA BLA BLA</li>
            </ul>
          </address>
          <hr width="100%" color="red" size="5" />
          <address class="norm">
            <h4>Experience</h4>
            <ul>
              <li>IT</li>
              <li>BLA BLA</li>
              <li>BLA BLA BLA</li>
            </ul>
          </address>
          <hr width="100%" color="red" size="5" />
          <address class="norm">
            <h4>Contacts</h4>
            <ul>
              <li>Email:test@example.com</li>
              <li>Telegram:</li>
              <li>Whatsapp:</li>
            </ul>
          </address>
        </div>
      </article>
      <aside>
        <h1><span>Additional Notes</span></h1>
        <div>
          <p>Created by Jobs and FreeLancing Application.</p>
        </div>
      </aside>
    </div>


    </body>
</html>
