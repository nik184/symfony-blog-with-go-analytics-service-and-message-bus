package main

import (
	"encoding/json"
	"fmt"
	"log"
	"math/rand"
	"net/http"
	"time"
)

type Post struct {
	AuthorID  int    `json:"authorId"`
	PostID    int    `json:"postId"`
	PostTitle string `json:"postTitle"`
	Action    string `json:"action"`
}

func main() {
	http.HandleFunc("/posts", posts)
	http.ListenAndServe(":8090", nil)
}

func posts(w http.ResponseWriter, req *http.Request) {

	if req.Method != http.MethodPost {
		w.WriteHeader(http.StatusBadRequest)
		fmt.Fprint(w, "Only POST requests are allowed")
		return
	}

	var post Post
	err := json.NewDecoder(req.Body).Decode(&post)
	if err != nil {
		w.WriteHeader(http.StatusBadRequest)
		fmt.Fprintf(w, "Failed to decode request body: %v", err)
		return
	}

	processingTime := time.Duration(rand.Intn(13)+3) * time.Second
	time.Sleep(processingTime)

	if post.PostID%3 == 0 {
		w.WriteHeader(http.StatusInternalServerError)
		log.Printf("Processing time: %v, HTTP status: %d", processingTime, http.StatusInternalServerError)
		fmt.Fprint(w, "Failed to process post")
	} else {
		w.WriteHeader(http.StatusOK)
		log.Printf("Processing time: %v, HTTP status: %d", processingTime, http.StatusOK)
		fmt.Fprint(w, "Post processed successfully")
	}
}
