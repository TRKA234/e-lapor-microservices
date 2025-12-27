FROM golang:1.21-alpine

WORKDIR /app

COPY . .

RUN rm -f go.mod && go mod init auth-service && go mod tidy

RUN go build -o main .

CMD ["./main"]