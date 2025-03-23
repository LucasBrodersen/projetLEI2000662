# projetLEI2000662

This project is a web-based platform designed for small and medium-sized businesses to manage subscriptions and recurring payments. The platform aims to automate billing processes, providing businesses like gyms, studios, consultants, and freelancers with an efficient way to handle subscription management, generate invoices, and track payments.

This project is being developed as the final project for the **Computer Engineering Course** at the **University of Aberta (UAB)**. It serves as a practical application of the knowledge acquired during the course, integrating key concepts from web development, payment systems, and software engineering.

## Features

- **Stripe Integration**: Automates recurring payments and subscription management.
- **Customer Area**: Customers can view their invoices, manage subscriptions, and update payment information.
- **Admin Panel**: Allows administrators to monitor active subscriptions, completed payments, overdue accounts, and financial metrics.
- **Modular System**: The system is adaptable and configurable to different business models.
  
## Technologies

- **Backend**: Laravel (PHP)
- **Frontend**: Vue.js
- **Payment Integration**: Stripe
- **Testing**: PHPUnit (unit tests), Cypress (end-to-end tests)
- **Development Tools**: Docker, WSL2, Git
- **Version Control**: Git (GitHub for repository management)
  
## Getting Started

### Prerequisites

Before you begin, make sure you have the following installed:

- PHP 8.x or higher
- Composer
- Node.js and npm
- Docker (for containerized development)
- WSL2 (for Windows development environments)
  
### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/your-repository-name.git
   ```

2. Navigate into the project directory:

   ```bash
   cd your-repository-name
   ```

3. Install backend dependencies:

   ```bash
   composer install
   ```

4. Install frontend dependencies:

   ```bash
   npm install
   ```

5. Set up the environment variables by duplicating `.env.example` to `.env` and adjusting the configuration (such as database and Stripe keys):

   ```bash
   cp .env.example .env
   ```

6. Run the application using Docker:

   ```bash
   docker-compose up
   ```

   This will start the backend and frontend services in containers.

### Running Tests

To run unit tests (PHPUnit):

```bash
php artisan test
```

To run end-to-end tests (Cypress):

```bash
npm run cypress:open
```

### Deployment

For production deployment, follow the guidelines for deploying Laravel applications to a web server, configuring your Stripe API keys, and ensuring secure handling of sensitive data.

## Development Process

This project follows an agile development process with regular iteration and testing. The work is divided into phases:

1. **Phase 1**: Requirements analysis, backend development (Laravel), and database architecture.
2. **Phase 2**: Stripe integration, invoice generation, and frontend development (Vue.js).
3. **Phase 3**: Testing, performance optimization, documentation, and project deployment.


## Acknowledgments

- [Stripe](https://stripe.com) for payment processing
- [Laravel](https://laravel.com) for backend development
- [Vue.js](https://vuejs.org) for frontend development
- [Cypress](https://www.cypress.io) for end-to-end testing
- [PHPUnit](https://phpunit.de) for unit testing
```

Make sure to replace the placeholders (like `yourusername` and `your-repository-name`) with actual values and adjust the steps as needed for your specific setup.
