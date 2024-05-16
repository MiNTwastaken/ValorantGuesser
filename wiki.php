<!DOCTYPE html>
<html>
<head>
    <title>Valorant Wiki</title>
    <link rel="stylesheet" href="styless.css">
</head>
<body>

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
                    <a href="wiki.php#strategies">Strategies</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="forum.php" class="dropdown-btn">Forum</a>
                <div class="dropdown-content">
                    <a href="forum.php#general">General Discussion</a>
                    <a href="forum.php#competitive">Competitive Play</a>
                    <a href="forum.php#lore">Lore & Story</a>
                    <a href="forum.php#creations">Community Creations</a>
                </div>
            </div>
            
            <div class="dropdown">
                <a href="minigames.php" class="dropdown-btn">Minigames</a>
                <div class="dropdown-content">
                    <a href="minigames.php#daily">Daily Quiz</a>
                    <a href="minigames.php#oneshot">One Shot</a>
                    <a href="minigames.php#freeplay">Free Play</a>
                </div>
            </div>

            <?php
            session_start();
            $isLoggedIn = isset($_SESSION["username"]);
            ?>

            <?php if ($isLoggedIn && isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) : ?>
                <div class="dropdown">
                    <a href="admin.php" class="dropdown-btn">Admin Panel</a>
                    <div class="dropdown-content">
                        <a href="admin.php#users">Manage Users</a> 
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

    <h2>Agents</h2>
    <div id="agents-container" class="container"></div>

    <h2>Weapons</h2>
    <div id="weapons-container" class="container"></div>

    <h2>Maps</h2>
    <div id="maps-container" class="container"></div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Function to create and display an item (agent, weapon, map)
        function createItem(item, type) {
            const itemDiv = document.createElement("div");
            itemDiv.classList.add("item", type);

            const itemName = document.createElement("h3");
            itemName.textContent = item.displayName;
            itemDiv.appendChild(itemName);

            const itemImage = document.createElement("img");
            itemImage.src = item.displayIcon;
            itemDiv.appendChild(itemImage);

            const itemDescription = document.createElement("p");
            itemDescription.textContent = item.description || item.tacticalDescription || "No description available";
            itemDiv.appendChild(itemDescription);

            return itemDiv;
        }
    

        // Function to fetch and display items of a specific type
        function fetchAndDisplayItems(url, containerId, type) {
            const container = document.getElementById(containerId);
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok.');
                    }
                    return response.json();
                })
                .then(data => {
                    data.data.forEach(item => {
                        if (type === 'agent' && !item.isPlayableCharacter) return;
                        const itemDiv = createItem(item, type);

                        // Display additional information based on item type
                        if (type === "agent") {
                            const agentRole = document.createElement("p");
                            agentRole.textContent = `Role: ${item.role.displayName}`;
                            itemDiv.appendChild(agentRole);

                            // Add abilities list for agents
                            const abilitiesList = document.createElement("ul");
                            item.abilities.forEach(ability => {
                                const abilityItem = document.createElement("li");
                                abilityItem.textContent = `${ability.displayName}: ${ability.description}`;
                                abilitiesList.appendChild(abilityItem);
                            });
                            itemDiv.appendChild(abilitiesList);
                        } else if (type === "weapon") {
                            // Handle weapon stats, checking if they exist
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
                            // Display map callouts
                            if (item.callouts) {
                                const calloutsList = document.createElement("ul");
                                calloutsList.classList.add("callouts"); // Add a class for callouts styling
                                item.callouts.forEach(callout => {
                                    const calloutItem = document.createElement("li");
                                    calloutItem.textContent = `${callout.regionName} (${callout.superRegionName})`;
                                    calloutsList.appendChild(calloutItem);
                                });
                                itemDiv.appendChild(calloutsList);
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

        // Fetch and display agents
        fetchAndDisplayItems('https://valorant-api.com/v1/agents', 'agents-container', 'agent');

        // Fetch and display weapons
        fetchAndDisplayItems('https://valorant-api.com/v1/weapons', 'weapons-container', 'weapon');

        // Fetch and display maps
        fetchAndDisplayItems('https://valorant-api.com/v1/maps', 'maps-container', 'map');
    });
</script>
</body>
</html>
