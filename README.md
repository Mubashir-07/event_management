# Event Management

### Setup and Installation

I'm using the Breeze starter kit

Clone the repo.
Setting up the `.env` file.
Run `php artisan migrate`.  
Run `npm run dev` and `php artisan serve` to serve the project.

---

#### There are 5 main sections:

1) Event Type  
2) Event  
3) Invitation  
4) Requisition  
5) Gallery  

---

1) **Auth**  
I chose the Breeze starter kit to get things moving quickly, but initially, it slowed me down because I had to understand how Blade components work. However, once I got the hang of it, the default styling and organized structure actually made development easier.

2) **Event Type**  
Added different event types (such as meetings, celebrations, etc.), which are used when creating new events.

3) **Event**  
Events can be created with all the necessary fields. In the database design, I set up a pivot table to link events with multiple users, including a status field to track each user's response. To control event visibility, I implemented a global scope, and for handling authorization based on specific conditions, I used policies.

4) **Gallery**  
In the Event table, there's a "Gallery" button that allows users to view images related to a specific event. Access to the gallery is restricted based on the event's visibility rules.

5) **Invitation**  
Invitations are displayed only to the users assigned to the event.

6) **Requisition List**  
In the requisition list for upcoming events, items are shown through a select input. The database follows a master-detail structure: a master record stores the event and its visibility, while sub-items include the item name and a "claimed" status. Users can view both public requisition lists and those specific to them. When marking an item as claimed, the system verifies whether the event is upcoming, whether the user is assigned to it, and allows items to be optionally marked as claimed.

---

### Challenges Faced

Working with Livewire was a new experience for me, and I gained a lot of valuable insights throughout the process.
The biggest challenge I faced was time—I wasn’t able to implement the edit and delete features within the given timeframe.

I also realized I need to deepen my understanding of separation of concerns and Livewire best practices. To improve in these areas, I plan to study production-level codebases.

One concern I had was Livewire's frequent AJAX requests on every state change, which raised questions about its scalability compared to client-side SPAs.
That said, I really enjoyed experimenting with Blade components and Livewire—it was both a learning experience and a lot of fun!

---

** And once again thank you for this opportunity!**
