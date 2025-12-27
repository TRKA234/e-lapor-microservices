const express = require("express");
const mongoose = require("mongoose");
const bodyParser = require("body-parser");
const app = express();

app.use(bodyParser.json());

// Koneksi ke MongoDB
const mongoUri = "mongodb://discussion-db:27017/db_discussion";
mongoose
  .connect(mongoUri)
  .then(() => console.log("Connected to MongoDB"))
  .catch((err) => console.error("MongoDB Connection Error:", err));

// Skema Komentar
const CommentSchema = new mongoose.Schema({
  report_id: Number,
  user: String,
  message: String,
  created_at: { type: Date, default: Date.now },
});
const Comment = mongoose.model("Comment", CommentSchema);

// Endpoint 1: Ambil semua komentar untuk satu laporan
app.get("/comments/:report_id", async (req, res) => {
  const comments = await Comment.find({ report_id: req.params.report_id });
  res.json(comments);
});

// Endpoint 2: Kirim komentar baru
app.get("/add-comment", async (req, res) => {
  const { report_id, user, msg } = req.query;
  if (!report_id || !user || !msg) {
    return res.send(
      "Gunakan parameter: ?report_id=1&user=Andi&msg=Segera diperbaiki"
    );
  }
  const newComment = new Comment({ report_id, user, message: msg });
  await newComment.save();
  res.json({ status: "Comment Saved!", data: newComment });
});

app.get("/", (req, res) =>
  res.send("Discussion Service (Node.js) is Running!")
);

app.listen(3000, "0.0.0.0", () => console.log("Server running on port 3000"));
