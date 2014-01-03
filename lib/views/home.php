<?php?>
<header class="header">
    <div class="sheet"></div>
</header>
<section id="main">
    <div class="article article-task sheet">
	<div class="boxAlternative">
	<h1 class="task-title">Task - Social Graph - Example Solution</h1>
	    <a href="#" class="show-graph">Show connection graph</a>
	    <div class="task-content">
		<div class="sheet">
		    <div class="columns menu">
			<div class="column3">
			    <button data-mode="friends" >Show Friends</button>
			</div>
			<div class="column3">
			    <button data-mode="friendsof" >Show Friends of</button>
			</div>
			<div class="column3">
			    <button data-mode="suggest" >Suggest Friends</button>
			</div>
		    </div>
		    <div class="columns users-list">
			<div class="column2">
			    <h3>Users</h3>
			    <ul id="users"></ul>
			</div>
			<div class="column2">
			    <h3>Results</h3>
			    <ul id="results"></ul>
			</div>
		    </div>
		    <div class="graph">
			<canvas id="viewport" width="900" height="600"></canvas>
		    </div>
		</div>
	    </div>
	</div>
    </div>
</section>
