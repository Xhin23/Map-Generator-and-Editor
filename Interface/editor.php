<div id="controls-wrapper">
<div id="controls" class="pos-top" style="position: fixed; top: 0px; left: 0px;">
<div id="menu" class="pane">
    <div class="row mode">
        <a pane="explore" href="" class="menusel"><i class="material-icons">search</i><span>Explore</span></a>
        <a pane="edit" href="" class="menusel"><i class="material-icons">brush</i><span>Edit Map</span></a>
        <a pane="rules" href="" class="menusel"><i class="material-icons">dashboard</i><span>Content Rules</span></a>
        <a alt_function="horizontal" href="" class="alt" style="" id="alt-horiz"><i class="material-icons">swap_horiz</i></a>
        <a alt_function="vertical" href="" class="alt" style=""><i class="material-icons">swap_vert</i></a>
        <a alt_function="opacity" href="" class="alt" style="" id="opacity"><i class="material-icons">flip_to_back</i></a>
        <a alt_function="fixed"  href="" class="sel"><i class="material-icons">zoom_out_map</i></a>
    </div>
</div>

<div id="top-message" style="display: none;"></div>
 
<div id="panes">
<div id="explore" class="pane" style="display: none;">
    <div id="explore-main" style="display: none;">
            <div class="help">
                Click a tile on the map to view or edit information about it.
            </div>
            <h3 id="regions-h3">View Region</h3>
            <div id="explore-region-buttons"></div>
    </div>
    
    <div id="explore-tile" style="display: none;">
        
        <div id="explore-tile-panes">
            <div id="explore-tile-display" style="display: none;">
                <div id="explore-name" class="explore-name"></div>
                <div id="explore-desc" class="explore-desc"></div>
                
                <input type="button" class="button" id="explore-edit" function="explore_edit" value="Edit Tile" />
            </div>
            
            <div id="explore-tile-edit" style="display: none;">
                <h2>Edit Tile</h2>
                <input id="explore-edit-name" placeholder="Name" /><br />
                <textarea id="explore-edit-desc" placeholder="Description"></textarea><br />
                <input type="button" class="button" id="save-explore-edit" function="save_explore_edit" value=" Save " />
                <div id="delete-regions-wrapper" style="display: none;">
                    <h3>Delete Regions</h3>
                    <div id="delete-regions"></div>
                </div>
            </div>
        </div>
        
        <table id="explore-rules"></table>
        <div id="explore-regions"><h3>Regions</h3><span></span></div>
    </div>
    
    <div id="explore-region" style="display: none;">
        <div id="explore-region-name" class="explore-name"></div>
        <div id="explore-region-desc" class="explore-desc"></div>
        <input type="button" class="button" id="explore-region-edit" function="edit_region" value="Edit Region" />
    </div>
</div>

<div id="rules" class="pane" style="display: none;">
    <a id="rules-back" href=""><i class="material-icons">arrow_back</i>Back</a>
    <div id="rules-main">
        <h2>Choose Rule Entity Type</h2>
        <div class="row"><h3>Terrain</h3><span id="rules-terrain" class="terrain-sel" type="terrain"></span></div>
        <div class="row"><h3>Foreground</h3><span id="rules-fg" class="fg-sel" type="fg"></span></div>
        <div class="row" id="choose-region"><h3>Region</h3><span id="rules-regions"></span></div>
    </div>
    
    <div id="rules-rules" style="display: none;">
        <div class="row">
            <h2>Add new rules</h2>
            <textarea id="add-rules" placeholder="Name -- Chance
Name -- Chance"></textarea><br />
            <input type="button" class="button" function="add_rules" value=" Add Rules " />
        </div>
        
        <div class="table-wrapper">
        <h2>Edit Rules</h2>
        <table id="rules-edit">
            <tr class="head">
                <th>Name</th>
                <th>Type</th>
                <th>Chance</th>
                <th>Seed</th>
                <th>Min</th>
                <th>Max</th>
                <th>Entries</th>
                <th>Delete</th>
            </tr>
            <tr class="template" style="display: none;">
                <td fname="name" ftype="input"><input class="f rules-name" /></td>
                <td fname="ruletype" ftype="input"><select class="f rules-ruletype">
                        <option value="trait">Trait</option>
                        <option value="numeric">Numeric</option>
                        <option value="items">Items</option>
                </select></td>
                <td fname="chance" ftype="chance"><input class="f rules-chance numeric" /></td>
                <td fname="seed" ftype="numeric"><input class="f rules-seed" /><input type="button" class="button button-el" function="reseed_rule" value="&#8635;" /></td>
                <td fname="min" ftype="numeric"><input class="f rules-min numeric" /></td>
                <td fname="max" ftype="numeric"><input class="f rules-max numeric" /></td>
                <td fname="entries" ftype="entries_link"><a href="" class="f rules-entries-link"></a><span class="rules-message" style="display: none;">Save to Edit</span></td>
                <td fname="name" ftype="delete"><label><input type="button" class="f button delete-button" function="delete_rule" value="Delete" /></td>
            </tr>
        </table>
        <input type="button" class="button" function="edit_rules" value=" Edit Rules " />
        </div>
    </div>
    
    <div id="rules-entries" style="display: none;">
        <div class="row">
            <h2>Add Entries</h2>
            <textarea id="add-entries" placeholder="Name -- Weight
Name -- Weight"></textarea><br />
            <input type="button" class="button" function="add_entries" value="Add Entries" />
        </div>
        
        <div class="table-wrapper">
        <h2>Edit Entries</h2>
        <table id="entries-edit">
            <tr class="head">
                <th>Name</th>
                <th>Weight</th>
                <th>Delete</th>
            </tr>
            <tr class="template" style="display: none;">
                <td fname="name" ftype="input"><input class="f entries-name" /></td>
                <td fname="weight" ftype="numeric"><input class="f entries-weight" /></td>
                <td fname="name" ftype="delete"><label><input type="button" class="f button delete-button" function="delete_entry" value="Delete" /></label></td>
            </tr>
        </table>
        <input type="button" class="button" function="edit_entries" value=" Edit Entries " />
        </div>
    </div>
</div>


<div id="edit" class="pane" style="display: none;">

<div class="row">
<h3>Tool</h3>
<div id="tools" class="mode">
    <a tool="point" href="" class="sel" id="tools-point"><i class="material-icons">filter_tilt_shift</i><span>Place</span></a>
    <a tool="line" href="" id="tools-line"><i class="material-icons">gesture</i><span>Line</span></a>
    <a tool="box" href="" id="tools-box"><i class="material-icons">crop_free</i><span>Box</span></a>
    <a tool="magic" href="" id="tools-magic"><i class="material-icons">star</i><span>Magic</span></a>
</div>
</div>

<div class="row" id="magic-settings" style="display: none;">
    <h3>Magic Settings</h3>
    <input type="button" class="switch-button sel" id="magic-terrain" value="Terrain: ON" switchto="Terrain: OFF" />
    <input type="button" class="switch-button" id="magic-fg" value="Foreground: OFF" switchto="Foreground: ON" />
    <input type="button" class="switch-button" id="magic-selbar" value="Selection Barrier: OFF" switchto="Selection Barrier: ON" />
</div>

<div class="row" id="edit-mode" style="display: none;">
<h3>Mode</h3> 
<input type="button" class="sel switch-button" id="select-mode" value="Select" switchto="Unselect" />
</div>

<div class="row" id="select-tools" style="display: none;">
    <h3>Selection Tools</h3>
    <input type="button" class="button" function="undo_selection" var1="terrain" value="Undo terrain" />
    <input type="button" class="button" function="undo_selection" var1="fg" value="Undo foreground" />
    <input type="button" class="button" function="revert" value="Revert tiles" />
    <input type="button" class="button" function="invert_selection" value="Invert" id="invert-sel" />
    <input type="button" class="button" function="clear_selection" value="Clear" />
</div>

<div class="row">
<h3>Terrain</h3>
<div class="img-edit terrain-sel" id="terrain-edit">
    <span id="edit-terrain-imgs" function="edit_terrain" condition="is_terrain" function2="undo_terrain"></span>
    <input type="button" class="button button-sprite" function="add_img" var1="terrain" value=" + " />
</div>
</div>

<div class="row" id="add-img" style="display: none;">
    <h3>New <span id="add-img-mode"></span></h3>
    <input id="add-img-url" placeholder="URL" />
    <span id="add-img-preview"></span>
    <input type="button" class="button" function="save_add_img" id="save-add-img" var1="" value=" Save " />
</div>

<div class="row">
<h3>Foreground</h3>
<div class="img-edit fg-sel" id="fg-edit">
    <span id="edit-fg-imgs" function="edit_fg" condition="has_fg" function2="undo_fg"></span>
    <input type="button" class="button button-sprite" function="add_img" var1="fg" value=" + " />
</div>    
</div>

<div class="row" id="region-tools">
    <h3>Region</h3>
    <input type="button" class="switch-button sel" id="edit-region-mode" value="Mode: Update Region" switchto="Mode: Select Region" />
    <div id="region-buttons"></div>
</div>

</div> 

<div class="row" id="alt-region" style="display: none;">
    <h2><span id="alt-region-mode"></span> Region</h2><br />
    <input id="alt-region-name" placeholder="Name" /><br />
    <input id="alt-region-color" placeholder="Color" class="jscolor" /><br />
    <textarea id="alt-region-desc" placeholder="Description"></textarea><br />
    <label id="alt-region-delete" style="display: none;"><input type="checkbox" />Delete</label>
    <input type="button" class="button" id="alt-region-button" value=" Save " />
</div>

</div>

<div id="bottom-message" style="display: none;"></div>

</div>
</div>