<!DOCTYPE html>
<html>
<head>
    <title>Menu Manager - API Tester</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { margin-top: 40px; }
        .output { background: #f4f4f4; padding: 10px; border: 1px solid #ccc; white-space: pre-wrap; }
        button { margin-right: 10px; padding: 6px 12px; }
    </style>
</head>
<body>

<h1>Menu Manager - API Tester</h1>

<h2>Test Get All</h2>
<button onclick="testGetAll('category')">Get All Categories</button>
<button onclick="testGetAll('menu_item')">Get All Menu Items</button>
<button onclick="testGetAll('ingredient')">Get All Ingredients</button>

<pre class="output" id="output"></pre>

<script>
function testGetAll(type) {
    fetch(`../API_1.php?action=get_all&type=${type}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('output').textContent = JSON.stringify(data, null, 4);
        })
        .catch(err => {
            document.getElementById('output').textContent = 'Error: ' + err;
        });
}
</script>

</body>
</html>
