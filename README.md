# Blog_Platform_API# Blog-Platform-API
# Blog Platform API (Laravel 11 + JWT Authentication)

A RESTful Blog Platform API built using **Laravel 11**, featuring:

- User authentication using **JWT**
- Role-based access (admin / user)
- CRUD operations on Blog posts
- Comments system
- Image upload
- Advanced blog filtering (search, category, author, date range)
- Caching for performance

---

## 🚀 Requirements

Make sure you have installed:

- PHP => 8.2  
- Composer  
- MySQL 
- Laravel 11  

---

## 📦 Installation & Setup

### 1️⃣ Clone the project
```bash
git clone <repo-url>
cd Blog_Platform


2️⃣ Install dependencies
composer install

3️⃣ Create .env file
cp .env.example .env

4️⃣ Generate application key
php artisan key:generate

🗄️ Database Setup
5️⃣ Update .env with your DB credentials:
DB_DATABASE=blog_paltform
DB_USERNAME=root
DB_PASSWORD=

6️⃣ Run migrations
php artisan migrate


7️⃣ Publish JWT config
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

8️⃣ Generate JWT secret key
php artisan jwt:secret



▶️ Running the API
10️⃣ Start the development server:
php artisan serve


Your API will run on:

http://localhost:8000

📡 API Endpoints
🔐 Authentication
Method	Endpoint	Description
POST	/api/register	User registration
POST	/api/login	Login (returns JWT token)
POST	/api/logout	Logout
📝 Blogs
Method	Endpoint	Description
GET	/api/posts	List all blogs (with search & filters)
GET	/api/posts/{id}	Get single blog
POST	/api/posts	Create blog (auth required)
PUT	/api/posts/{id}	Update blog (owner/admin)
DELETE	/api/posts/{id}	Delete blog (owner/admin)


🔑 How to Use JWT Token

Whenever you login, you receive a token.
Add it to your request headers:

Authorization: Bearer <your-token>
Accept: application/json

🧪 Testing With Postman / Thunder Client

I included a full guide for:

Register

Login

Attach token

Create blog with image

Update / delete blog

Add comments

✅ Conclusion

The Blog Platform API is now fully functional with:

JWT authentication

Role-based access

Blog + Comments CRUD

Filtering & search

Image upload & storage

Caching
