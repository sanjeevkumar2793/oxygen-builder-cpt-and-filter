# Oxygen Builder - Health Course Finder (HCS) Integration Guide

This guide explains how to implement the custom Course Finder interface and dynamic filtering layout using **Oxygen Builder** and **Advanced Custom Fields (ACF)**.

---

## 📌 Prerequisites & Requirements

* **WordPress** with active **Oxygen Builder** plugin.
* **Advanced Custom Fields (ACF) Pro** (**Compulsory** for defining Course CPT/Custom Fields such as Date, Location, and Format).
* Basic knowledge of Oxygen Builder structure elements (`Code Block`, `Repeater` / `Easy Posts`).

---

## 📂 Implementation Steps in Oxygen Builder

Follow these exact steps inside Oxygen Builder editor to structure the code and elements correctly:

---

### Step 1: Add the HTML Finder Interface

1. In your Oxygen Builder Page/Template editor, add a **Code Block** element where you want the filter bar to appear.
2. Go to **PHP & HTML** tab of the Code Block and paste the following markup:

```html
<div class="hcs-finder">
  <h3 class="hcs-finder-title">Find a course</h3>
  <div class="hcs-finder-grid">
    <div class="hcs-field">
      <label for="finder-course">Course</label>
      <select id="finder-course">
        <option value="all">All courses</option>
        <option value="Intermediate First Aid">Intermediate First Aid</option>
        <option value="Basic First Aid">Basic First Aid</option>
        <option value="CPR/AED Level C">CPR/AED Level C</option>
        <option value="BLS Provider">BLS Provider</option>
        <option value="ACLS Provider">ACLS Provider</option>
        <option value="PALS Provider">PALS Provider</option>
        <option value="Mask Fit Testing">Mask Fit Testing</option>
        <option value="WHMIS Training">WHMIS Training</option>
      </select>
    </div>
    <div class="hcs-field">
      <label for="finder-city">City</label>
      <select id="finder-city">
        <option value="all">All GTA locations</option>
        <option value="North York">North York</option>
        <option value="Mississauga">Mississauga</option>
        <option value="Brampton">Brampton</option>
        <option value="Scarborough">Scarborough</option>
        <option value="Etobicoke">Etobicoke</option>
      </select>
    </div>
    <div class="hcs-field">
      <label for="finder-date">Date from</label>
      <input id="finder-date" type="date">
    </div>
    <div class="hcs-field">
      <label for="finder-format">Format</label>
      <select id="finder-format">
        <option value="all">Any format</option>
        <option value="In-class">In-class</option>
        <option value="Blended">Blended</option>
        <option value="Online">Online</option>
        <option value="On-site">On-site</option>
      </select>
    </div>
  </div>
  <p class="hcs-count" aria-live="polite"></p>
</div>
Step 2: Configure the Post Repeater & Apply hcs-list Class
Add a Repeater element (or Easy Posts) in Oxygen Builder to query your courses loop.

Select the parent Repeater element in the Oxygen structure panel.

In the left panel, go to Advanced > Attributes / Classes.

Add the class name: hcs-list

Inside the Repeater item div, output your ACF post data (Course Title, Location, Date, Format) as data-attributes or text tags depending on your JS filtering logic.
