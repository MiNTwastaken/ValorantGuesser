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
        .read-more-btn {
            background-color: #FF4654; /* Valorant Red */
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            display: block; /* Make it take up full width */
            margin-top: auto; /* Push to bottom */
        }

        .read-more-btn:hover {
            background-color: #D93645; /* Slightly darker red on hover */
        }

    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
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
                const MAX_DESCRIPTION_LENGTH = 100;
                const MAX_ABILITIES_DISPLAYED = 1; // Number of abilities to show before "Read More"

                // Function to create and display an item (agent, weapon, map or skin)
                function createItem(item, type) {
                    const itemDiv = document.createElement("div");
                    itemDiv.classList.add("item", type);

                    const itemName = document.createElement("h3");
                    itemName.textContent = item.displayName;
                    itemDiv.appendChild(itemName);

                    const itemImage = document.createElement("img");
                    itemImage.src = item.displayIcon;
                    itemDiv.appendChild(itemImage);

                    // Description handling (for agents, weapons, maps)
                    const fullDescription = item.description || item.tacticalDescription || "No description available";
                    const itemDescription = document.createElement("p");
                    if (fullDescription.length > MAX_DESCRIPTION_LENGTH) {
                        itemDescription.textContent = fullDescription.substring(0, MAX_DESCRIPTION_LENGTH) + "...";
                        const readMoreButton = document.createElement("button");
                        readMoreButton.textContent = "Read More";
                        readMoreButton.classList.add("read-more-btn");
                        readMoreButton.addEventListener("click", () => {
                            window.location.href = `wiki_details.php?uuid=${item.uuid}&type=${type}`;
                        });
                        itemDiv.appendChild(readMoreButton);
                    } else {
                        itemDescription.textContent = fullDescription;
                    }
                    itemDiv.appendChild(itemDescription);

                    // Handle abilities (for agents)
                    if (type === "agent") {
                        const agentRole = document.createElement("p");
                        agentRole.textContent = `Role: ${item.role.displayName}`;
                        itemDiv.appendChild(agentRole);
                        
                        const abilitiesList = document.createElement("ul");
                        let abilityCount = 0; 
                        item.abilities.forEach(ability => {
                            if (ability.slot !== "Passive" && abilityCount < MAX_ABILITIES_DISPLAYED) {
                                const abilityItem = document.createElement("li");
                                abilityItem.textContent = `${ability.displayName}: ${ability.description}`;
                                abilitiesList.appendChild(abilityItem);
                                abilityCount++;
                            }
                        });

                        itemDiv.appendChild(abilitiesList);
                    }

                    // Handle weapon stats (for weapons)
                    if (type === "weapon" && item.weaponStats) {
                        const weaponStats = document.createElement("ul");
                        weaponStats.innerHTML = `
                            <li>Fire Rate: ${item.weaponStats.fireRate}</li>
                            <li>Magazine Size: ${item.weaponStats.magazineSize}</li>
                            <li>Cost: ${item.shopData?.cost ?? "N/A"}</li>
                        `; 
                        itemDiv.appendChild(weaponStats);
                    } 

                    // Handle map callouts (for maps)
                    if (type === "map" && item.callouts) {
                        const calloutsList = document.createElement("ul");
                        calloutsList.classList.add("callouts");
                        item.callouts.forEach(callout => {
                            const calloutItem = document.createElement("li");
                            calloutItem.textContent = `${callout.regionName} (${callout.superRegionName})`;
                            calloutsList.appendChild(calloutItem);
                        });
                        itemDiv.appendChild(calloutsList);
                    }

                    // Handle skins (for skins)
                    if (type === "skin") {
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

                    return itemDiv; 
                }

                // Function to fetch and display items of a specific type
                function fetchAndDisplayItems(url, containerId, type) {
                    const container = document.getElementById(containerId);
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            data.data.forEach(item => {
                                if (type === 'agent' && !item.isPlayableCharacter) return;
                                const itemDiv = createItem(item, type);
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

                    containers.forEach(containerId => {
                        const container = document.getElementById(containerId);
                        container.style.display = containerId.startsWith(hash) || hash === "" ? "grid" : "none";
                    });
                }

                filterContent(); 
                window.addEventListener('hashchange', filterContent); 

                fetchAndDisplayItems('https://valorant-api.com/v1/agents', 'agents-container', 'agent');
                fetchAndDisplayItems('https://valorant-api.com/v1/weapons', 'weapons-container', 'weapon');
                fetchAndDisplayItems('https://valorant-api.com/v1/maps', 'maps-container', 'map');
                fetchAndDisplayItems('https://valorant-api.com/v1/weapons/skins', 'skins-container', 'skin');
        });

    </script>
</body>
</html>
