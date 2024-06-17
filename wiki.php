<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Wiki</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .wiki-container {
            display: block; 
        }

        #agents-container,
        #weapons-container,
        #maps-container,
        #skins-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .item {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }

        .item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .item img { 
            max-width: 100%;
            height: auto;
            border-radius: 1rem; /* Rounded corners top left and top right */
            margin-top: 10px; /* Add margin to top */
            margin-bottom: 10px; /* Add margin to bottom */
            border:0.3rem solid #FF4654;

        }

        .item ul, .item li {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .item::after {
            content: "";
            display: table;
            clear: both;
        }

        .item p, .item ul, .item li {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .item h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .item p {
            font-size: 0.9rem; 
        }

        .item ul {
            list-style: disc;
            padding-left: 20px;
        }

        .item li {
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .item .callouts {
            list-style: none;
            padding: 0;
        }

        .item .callouts li {
            font-size: 0.8rem;
        }

        .read-more-btn {
            background-color: #FF4654;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            margin-top: auto;
        }

        .read-more-btn:hover {
            background-color: #D93645;
        }
        .seethrough{
            background-color: rgba(248, 249, 250, 0.9);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-sm-5 p-5 rounded-lg seethrough">
        <h1>Valorant Wiki</h1>
        <p>Welcome to the Valorant Wiki! Here you'll find comprehensive information about the game.</p>
        <div class="wiki-container">
            <div id="agents-container" class="mb-5"></div>
            <div id="weapons-container" class="mb-5"></div>
            <div id="maps-container" class="mb-5"></div>
            <div id="skins-container" class="mb-5"></div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const MAX_DESCRIPTION_LENGTH = 100;
            const MAX_ABILITIES_DISPLAYED = 1;

            function createItem(item, type) {
                const itemDiv = document.createElement("div");
                itemDiv.classList.add("item", "col");

                const itemName = document.createElement("h3");
                itemName.textContent = item.displayName;
                itemDiv.appendChild(itemName);

                const itemImage = document.createElement("img");
                itemImage.src = item.displayIcon;
                itemDiv.appendChild(itemImage);

                const fullDescription = item.description || item.tacticalDescription || "No description available";
                const itemDescription = document.createElement("p");
                if (fullDescription.length > MAX_DESCRIPTION_LENGTH) {
                    itemDescription.textContent = fullDescription.substring(0, MAX_DESCRIPTION_LENGTH) + "...";
                } else {
                    itemDescription.textContent = fullDescription;
                }
                itemDiv.appendChild(itemDescription);

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
                    const readMoreButton = document.createElement("button");
                    readMoreButton.textContent = "Read More";
                    readMoreButton.classList.add("read-more-btn");
                    readMoreButton.addEventListener("click", () => {
                        window.location.href = `wiki_details.php?uuid=${item.uuid}&type=${type}`;
                    });
                    itemDiv.appendChild(abilitiesList);
                    itemDiv.appendChild(readMoreButton);
                    
                }

                if (type === "weapon" && item.weaponStats) {
                    const weaponStats = document.createElement("ul");
                    weaponStats.innerHTML = `
                        <li>Fire Rate: ${item.weaponStats.fireRate}</li>
                        <li>Magazine Size: ${item.weaponStats.magazineSize}</li>
                        <li>Cost: ${item.shopData?.cost ?? "N/A"}</li>
                    `;
                    itemDiv.appendChild(weaponStats);
                }

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
                const hash = window.location.hash.substring(1);
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
