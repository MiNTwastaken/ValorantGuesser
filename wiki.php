<!DOCTYPE html>
<html>
<head>
    <title>Valorant Wiki</title>
    <link rel="stylesheet" href="styless.css"> 
    <style>
        .content .wiki-container {
        display: block; /* Reset the main container to be a block element */
        }

        /* Individual category container styles */
        #agents-container,
        #weapons-container,
        #maps-container,
        #skins-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* 3 columns, responsive */
        gap: 20px;
        margin: 20px 0;
        }

        /* Item styles */
        .item {
        border: 1px solid #ddd;
        padding: 15px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
        }

        .item:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .item img { /* Constrain image size within the item */
        max-width: 100%;
        height: auto;
        }

        .item ul, .item li { /* Reset list styles to avoid extra space */
        list-style: none;
        padding: 0;
        margin: 0;
        }
        .item::after { /* Clear floats after the item content */
        content: "";
        display: table;
        clear: both;
        }
        /* General text styles */
        .item p, .item ul, .item li {
        font-family: sans-serif; 
        line-height: 1.6; 
        color: #333;
        }

        /* Heading styles */
        .item h3 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        }

        /* Description styles */
        .item p {
        font-size: 0.9rem; 
        }

        /* List styles (for abilities, weapon stats, etc.) */
        .item ul {
        list-style: disc; /* Use bullet points */
        padding-left: 20px;
        }

        .item li {
        font-size: 0.85rem;
        margin-bottom: 5px; 
        }

        /* Callout list styles (specific to maps) */
        .item .callouts {
        list-style: none;
        padding: 0;
        }

        .item .callouts li {
        font-size: 0.8rem;
        }

    </style>
</head>
<body>
    <?php
    session_start();
    ?>
    <div class="background-video">
        <video autoplay muted loop>
            <source src="content/illustration.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="navbar">
        <div class="container">
            <a href="index.php">Valorant Fanpage</a>
            <nav>
                <div class="dropdown">
                    <a href="wiki.php" class="dropdown-btn">Wiki</a>
                    <div class="dropdown-content">
                        <a href="wiki.php#agents">Agents</a>
                        <a href="wiki.php#weapons">Weapons</a>
                        <a href="wiki.php#maps">Maps</a>
                        <a href="wiki.php#skins">Skins</a>
                    </div>
                </div>

                <div class="dropdown">
                    <a href="social.php" class="dropdown-btn">Social</a>
                    <div class="dropdown-content">
                        <a href="social.php#general">General Discussion</a>
                        <a href="social.php#competitive">Competitive Play</a>
                        <a href="social.php#lore">Lore & Story</a>
                        <a href="social.php#creations">Community Creations</a>
                    </div>
                </div>
                
                <div class="dropdown">
                    <a href="minigames.php" class="dropdown-btn">Minigames</a>
                    <div class="dropdown-content">
                        <a href="dailychallenge.php">Daily Quiz</a>
                        <a href="aimtrainer.php">One Shot</a>
                        <a href="freeplay.php">Free Play</a>
                        <a href="leaderboard.php">Leaderboard</a>
                    </div>
                </div>

                <?php
                $isLoggedIn = isset($_SESSION["username"]);
                ?>

                <?php if ($isLoggedIn && isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) : ?>
                    <div class="dropdown">
                        <a href="admin.php" class="dropdown-btn">Admin Panel</a>
                        <div class="dropdown-content">
                            <a href="admin.php">Manage Users</a>
                            <a href="gamedata.php">Manage Game Data</a>
                            <a href="posts.php">Manage Posts</a>
                        </div>
                    </div>
                <?php endif; ?>


                <?php if ($isLoggedIn) : ?>
                    <div class="logged-in-user">
                        <a href="profile.php" class="profile-link"><?php echo $_SESSION["username"]; ?></a>
                        <form action="logout.php" method="post">
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                <?php else : ?>
                    <a href="login.php" class="login-btn">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    <div class="content">
    <h1>Valorant Wiki</h1>
    <p>Welcome to the Valorant Wiki! Here you'll find comprehensive information about the game.</p>
    <div class="wiki-container">
        <div id="agents-container"></div>
        <div id="weapons-container"></div>
        <div id="maps-container"></div>
        <div id="skins-container"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function createItem(item, type) {
                const itemDiv = document.createElement("div");
                itemDiv.classList.add("item", type);

                const itemName = document.createElement("h3");
                itemName.textContent = item.displayName;
                itemDiv.appendChild(itemName);

                const itemImage = document.createElement("img");
                itemImage.src = item.displayIcon;
                itemDiv.appendChild(itemImage);

                const descriptionText = item.description || item.tacticalDescription;
                if (descriptionText) {
                    const itemDescription = document.createElement("p");
                    itemDescription.textContent = descriptionText;
                    itemDiv.appendChild(itemDescription);
                }

                return itemDiv;
            }

            function fetchAndDisplayItems(url, containerId, type) {
                const container = document.getElementById(containerId);
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        data.data.forEach(item => {
                            if (type === 'agent' && !item.isPlayableCharacter) return;
                            const itemDiv = createItem(item, type);

                            if (type === "agent") {
                                const agentRole = document.createElement("p");
                                agentRole.textContent = `Role: ${item.role.displayName}`;
                                itemDiv.appendChild(agentRole);
                                const abilitiesList = document.createElement("ul");
                                item.abilities.forEach(ability => {
                                    const abilityItem = document.createElement("li");
                                    abilityItem.textContent = `${ability.displayName}: ${ability.description}`;
                                    abilitiesList.appendChild(abilityItem);
                                });
                                itemDiv.appendChild(abilitiesList);
                            } else if (type === "weapon") {
                                if (item.weaponStats) {
                                    const weaponStats = document.createElement("ul");
                                    weaponStats.innerHTML = `
                                        <li>Fire Rate: ${item.weaponStats.fireRate}</li>
                                        <li>Magazine Size: ${item.weaponStats.magazineSize}</li>
                                        <li>Cost: ${item.shopData?.cost ?? "N/A"}</li>
                                    `;
                                    itemDiv.appendChild(weaponStats);
                                } else {
                                    const noStatsMessage = document.createElement("p");
                                    noStatsMessage.textContent = "Weapon stats not available.";
                                    itemDiv.appendChild(noStatsMessage);
                                }
                            } else if (type === "map") {
                                if (item.callouts) {
                                    const calloutsList = document.createElement("ul");
                                    calloutsList.classList.add("callouts");
                                    item.callouts.forEach(callout => {
                                        const calloutItem = document.createElement("li");
                                        calloutItem.textContent = `${callout.regionName} (${callout.superRegionName})`;
                                        calloutsList.appendChild(calloutItem);
                                    });
                                    itemDiv.appendChild(calloutsList);
                                }
                            } else if (type === "skin") {
                                const levelsList = document.createElement("ul");
                                item.levels.forEach(level => {
                                    const levelItem = document.createElement("li");
                                    levelItem.textContent = level.displayName;
                                    levelsList.appendChild(levelItem);
                                });
                                itemDiv.appendChild(levelsList);

                                if (item.chromas && item.chromas.length > 0) {
                                    const chromasList = document.createElement("ul");
                                    item.chromas.forEach(chroma => {
                                        const chromaItem = document.createElement("li");
                                        chromaItem.textContent = chroma.displayName;
                                        chromasList.appendChild(chromaItem);
                                    });
                                    itemDiv.appendChild(chromasList);
                                }
                            }
                            container.appendChild(itemDiv);
                        });
                    })
                    .catch(error => {
                        container.innerHTML = "<p>Error fetching data: " + error.message + "</p>";
                        console.error('Fetch Error:', error); 
                    });
            }

            function filterContent() {
                const hash = window.location.hash.substring(1); // Get the hash (e.g., 'skins')
                const containers = ["agents-container", "weapons-container", "maps-container", "skins-container"];

                // Show/hide containers
                containers.forEach(containerId => {
                    const container = document.getElementById(containerId);
                    container.style.display = containerId.startsWith(hash) || hash === "" ? "grid" : "none";
                });
            }

            // Call filterContent on page load AND on hash change
            filterContent(); // Initial filtering
            window.addEventListener('hashchange', filterContent); // Listen for changes

            fetchAndDisplayItems('https://valorant-api.com/v1/agents', 'agents-container', 'agent');
            fetchAndDisplayItems('https://valorant-api.com/v1/weapons', 'weapons-container', 'weapon');
            fetchAndDisplayItems('https://valorant-api.com/v1/maps', 'maps-container', 'map');
            fetchAndDisplayItems('https://valorant-api.com/v1/weapons/skins', 'skins-container', 'skin');
        });
    </script>
</body>
</html>
