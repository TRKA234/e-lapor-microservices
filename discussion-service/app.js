const express = require("express");
const mongoose = require("mongoose");
const app = express();

// URL database mengarah ke nama service di docker-compose
const mongoUri = "mongodb://discussion-db:27017/db_discussion";

mongoose
  .connect(mongoUri)
  .then(() => console.log("Berhasil terkoneksi ke MongoDB"))
  .catch((err) => console.error("Gagal koneksi ke MongoDB:", err));

app.get("/", (req, res) => {
  res.json({
    service: "Discussion Service",
    status: "Active",
    database: "MongoDB Connected",
  });
});

app.listen(3000, "0.0.0.0", () => {
  console.log("Discussion service running on port 3000");
});
