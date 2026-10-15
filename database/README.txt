You normally do NOT need this folder: setup.php creates everything automatically.

Optional manual import using MongoDB Compass:
  1. Connect to mongodb://localhost:27017
  2. Create database "eventms", collection "events"
  3. ADD DATA > Import JSON or CSV file > choose events_sample.json

Useful mongosh commands for the viva:
  use eventms
  show collections
  db.events.find().pretty()
  db.users.find({ role: "student" })
  db.registrations.countDocuments()
