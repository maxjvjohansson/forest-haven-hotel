# Forest Haven Hotel

**Forest Haven Hotel** is a fictional hotel located on a serene island surrounded by lush forests and calm waters. This project allows users to book rooms, select additional services, and to manage reservations for an unforgettable stay.

---
## Jennies comments

- **store.php line 33-38** it's nice but I don't see why you would need this and not just add a "required" in the tags on the HTML-form? Then you can target it with CSS as well (if you would like to).

- **Over all queries in php to the DB** Some times you use the $query->bindParam and other times you bind the parameter in the execute. I personaly prefere doing it in the execute, as it's one step less and still good readability if you "indent" right.

- **get_bookings.php** I don't get why this function is in a sepperate file and not a part of the functions.php?

- **Book now in the nav** This link isn't smooth scroll it's a jump(default on anchor links). It would be nice if that link also scrolled like the CTA-button, since your page has a full-screen hero.

- **header.php line 31-52** Why is the nav in a div with the logo on the outside? I would recomend having everything in a nav, skip the div and put justify-self: end; on the logo.

- **footer.php** I would use <section> instead as it's a bit more symantic.

- **index.php line 54** Why do you have a <span> within a <p> on the enitre text? I recomend using a class on the <p> tag instead.
---

## Features

- **Room Booking**: Choose from a variety of rooms, including Woodland Retreat, Forest Haven, and Canopy Grand Suite.
- **Additional Services**: Add features like spa treatments, breakfast buffets, and guided forest hikes.
- **Real-time Availability**: View room availability through an interactive calendar.
- **Secure Payment Integration**: Validate transfer codes securely via the **Central Bank of Yrgopelago** API.
- **Discounts**: Enjoy package discounts for a more affordable stay.
- **Admin Page**: Manage bookings, dynamically update pricing, and adjust the hotel’s star rating all in one place.

---

## Technologies Used

- **HTML**: Website structure and content.
- **CSS**: Responsive and stylish design.
- **PHP**: Backend logic and API integration.
- **SQL**: Database for storing room availability and booking details.
- **JavaScript**: Dynamic interactions like calendars and price calculations.
- **GitHub**: Version control and project repository.
- **One.com**: Deployment.

---

## Installation

### Prerequisites

- Ensure you have **PHP** installed.
- Have **SQLite** enabled in your PHP configuration.

### Step-by-Step Instructions

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/yourusername/forest-haven-hotel.git
   cd forest-haven-hotel
   ```

2. **Set Up the Environment File**:

   - Create a `.env` file in the root directory based on `.env.example`.
   - Fill in the required fields, such as your database name and API key.

   Example `.env` file:
   ```env
   DB_NAME=forest_haven
   API_KEY=your_api_key_here
   ```

3. **Set Up the Database**:

   - Use the provided SQL queries (`database.sql`) to create and populate the database.
   - Run the following command in the root directory:

     ```bash
     sqlite3 forest_haven.db < database.sql
     ```

4. **Start the Local Server**:

   - Using PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```

5. **Access the Website**:

   - Open your browser and navigate to `http://localhost:8000/index.php`.

---

## Database Structure

### Tables

- **`rooms`**: Stores room types, names, and prices.
- **`features`**: Lists additional services like spa treatments.
- **`bookings`**: Tracks reservations, guest details, and payment information.
- **`booking_feature`**: Links bookings to additional services.
- **`admin_settings`**: Manages discounts and configurations.
- **`discount_rooms`**: Tracks rooms eligible for discounts.

### SQL Queries to Recreate the Database

```sql
-- ROOMS
CREATE TABLE rooms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT NOT NULL,
    name TEXT NOT NULL,
    price INTEGER NOT NULL
);

-- FEATURES
CREATE TABLE features (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    price INTEGER NOT NULL
);

-- BOOKINGS
CREATE TABLE bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    room_id INTEGER NOT NULL,
    room_price INTEGER NOT NULL,
    guest_name TEXT NOT NULL,
    arrival_date DATE NOT NULL,
    departure_date DATE NOT NULL,
    total_cost INTEGER NOT NULL,
    transfer_code TEXT NOT NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- BOOKING FEATURE (JUNCTION)
CREATE TABLE booking_feature (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    booking_id INTEGER NOT NULL,
    feature_id INTEGER NOT NULL,
    feature_cost INTEGER NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (feature_id) REFERENCES features(id)
);

-- ADMIN SETTINGS
CREATE TABLE admin_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    stars INTEGER NOT NULL,
    discount_min_days INTEGER DEFAULT 3,
    discount_feature_name TEXT DEFAULT NULL,
    FOREIGN KEY (discount_feature_name) REFERENCES features(name)
);

-- DISCOUNT ROOMS
CREATE TABLE discount_rooms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    admin_setting_id INTEGER NOT NULL,
    room_name TEXT NOT NULL,
    is_active BOOLEAN DEFAULT 0,
    FOREIGN KEY (admin_setting_id) REFERENCES admin_settings(id),
    FOREIGN KEY (room_name) REFERENCES rooms(name)
);

-- ROOM INSERTS
INSERT INTO rooms (type, name, price) VALUES
('Budget', 'Woodland Retreat', 2),
('Standard', 'Forest Haven', 3),
('Luxury', 'Canopy Grand Suite', 5);

-- FEATURES INSERTS
INSERT INTO features (name, price) VALUES
('Forest-Themed Breakfast Buffet', 1),
('Nature-Inspired Spa Retreat', 2),
('Guided Forest Hike', 1),
('Tree Canopy Adventure Course', 2);

-- ADMIN SETTINGS INSERTS
INSERT INTO admin_settings (stars, discount_feature_name, discount_min_days)
VALUES
(3, 'Nature-Inspired Spa Retreat', 3);

-- DISCOUNT_ROOMS INSERTS
INSERT INTO discount_rooms (admin_setting_id, room_name, is_active)
SELECT 1, name, 0 FROM rooms;
```

---

## API Integration

The system integrates with the **Central Bank of Yrgopelago** to validate payments and manage hotel registration.

### Endpoints

- **`/transferCode`**: Validates a transfercode for payment.
- **`/deposit`**: Consumes the transfercode into money.

### API Key Management

- Store your **API_KEY** securely in the `.env` file.
- Never expose the `.env` file or sensitive details publicly.

---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## Credits

- **Central Bank of Yrgopelago** for API integration.

---

Enjoy your stay at **Forest Haven Hotel**!

