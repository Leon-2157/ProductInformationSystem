## Product Information System

This project is the design of a ready to use product information data management system structure, by applying the theory concept of separation of concerns to three main layers, namely data, logic and display using pure (original) PHP without the help of external databases. Instead, data is temporarily stored using a multidimensional array structure. This system displays a list of products into an HTML table and is equipped with a total stock asset calculation feature.

## Architecture

Based on its architectural division, this project consists of three main files:

| File | Layer | Description |
| --- | --- | --- |
| `products.php` | Data Layer | Contains an array of product commodity data. |
| `functions.php` | Processing Layer | Contains warehouse asset calculation functions and logic for critical stock filters. |
| `index.php` | Presentation Layer | Main file to render the table interface and combine all components. |

## Requirements

- Docker
- Docker Compose

## Getting Started

Clone the repository and start the container:

```bash
git clone https://github.com/Leon-2157/Desain-Product-Information-System-Public.git
cd Desain-Product-Information-System-Public
docker compose up -d
```

Access the application at [http://localhost:8080](http://localhost:8080).

To stop the container:

```bash
docker compose down
```

## Repository Structure

```text
ProductInformationSystem/
├── products.php        # Data Layer
├── functions.php       # Processing Layer
├── index.php           # Presentation Layer
├── style.css           # Styling for HTML interface
├── Dockerfile          # Docker image creation instructions
├── docker-compose.yml  # Orchestration of container services
├── .dockerignore       # Files excluded from Docker context
├── desain.md           # System architecture document
└── README.md           # Main Documentation
```

## Features

- Displays all products in a structured HTML table
- Calculates total warehouse asset value via `hitung_total_nilai_stok()`
- Highlights critical stock rows (stock < 3) with a distinct background color
- Formats all prices in Indonesian Rupiah
- Serves via Apache inside an isolated Docker container
- Volume mount enables live code changes without container rebuild

## Stack

| Component | Technology       |
|-----------|-----------------|
| Language  | PHP 8.2          |
| Server    | Apache (built-in)|
| Container | Docker           |
| Compose   | Docker Compose v2|

## License

MIT
