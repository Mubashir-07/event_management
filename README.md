# Event Management

### Setup and Installation Process

I'm using the Breeze starter kit, so:

Cloning the repo, setting up the `.env` file, and running `php artisan migrate` is enough.  
After that, I run `npm run dev` and `php artisan serve` to serve the project.

---

### Walkthrough and Assumptions

#### There are 5 main sections:

1) Event Type  
2) Event  
3) Invitation  
4) Requisition  
5) Gallery  

---

1) **Auth**  
I went with the Breeze starter kit to speed up the process, but it actually slowed me down at first because I had to learn about Blade components. But later on, it made things easier because of some default styles and structure.

2) **Event Type**  
I added event types (like meeting, celebration, etc.). It's used while creating events.

3) **Event**  
We can create an event with the required fields. In the database design, I've created a pivot table to connect events with other users. The pivot table also includes a `status` field.  
To restrict events, I implemented a global scope. For checking certain conditions, I used policies.

4) **Gallery**  
In the same Event table, there is a "Gallery" button to access images related to that event. Access to the gallery is restricted based on the event.

5) **Invitation**  
Invitations are displayed only to the users assigned to the event.

6) **Requisition List**  
In the requisition list for upcoming events, the items are displayed in a select input.  
The database design follows a master-sub structure: a master entry for the event and its visibility, and sub-items that include a name and a "claimed" flag.  
Users can see both public and their private requisition lists.  
When marking an item as claimed, the system checks if the event is upcoming, if the user is assigned to it, and even allows optional marking as claimed.

---

### Difficulties

Livewire was a new thing for me, and I learned a lot through the process.  
The only real barrier was **time!** I didn’t get the chance to implement edit and delete features.  

Also, I still need to learn more about **code separation of concerns** and **Livewire code standards**. I plan to explore some production codebases for that.

One thing I was concerned about was Livewire sending AJAX requests for every state change. This made me question its scalability compared to client-side SPAs.  
But overall, I had so much fun experimenting with Blade components and Livewire!

---

**Thank you for this opportunity!**
