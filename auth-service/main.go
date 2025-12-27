package main

import (
	"database/sql"
	"fmt"
	"log"
	"net/http"

	_ "github.com/lib/pq"
)

func main() {
	// Pastikan sslmode=disable ada di sini
	dbURL := "postgres://user_auth:pass_auth@auth-db:5432/db_auth?sslmode=disable"
	db, err := sql.Open("postgres", dbURL)
	if err != nil {
		log.Fatal(err)
	}

	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		err := db.Ping()
		if err != nil {
			fmt.Fprintf(w, "Auth Service Active, tapi GAGAL konek Postgres: %v", err)
		} else {
			fmt.Fprintf(w, "Auth Service Active & TERKONEKSI ke Postgres!")
		}
	})

	fmt.Println("Auth Service running on :8080")
	http.ListenAndServe(":8080", nil)
}