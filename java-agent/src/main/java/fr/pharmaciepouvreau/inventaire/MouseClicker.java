package fr.pharmaciepouvreau.inventaire;

import java.awt.AWTException;
import java.awt.KeyEventDispatcher;
import java.awt.KeyboardFocusManager;
import java.awt.Robot;
import java.awt.event.InputEvent;
import java.awt.event.KeyEvent;
import java.util.logging.Level;
import java.util.logging.Logger;

import org.jnativehook.GlobalScreen;
import org.jnativehook.keyboard.NativeKeyEvent;
import org.jnativehook.keyboard.NativeKeyListener;

public class MouseClicker implements NativeKeyListener {
	

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		try {
			GlobalScreen.registerNativeHook();
			GlobalScreen.addNativeKeyListener(new MouseClicker());
			
			// Get the logger for "org.jnativehook" and set the level to off.
			Logger logger = Logger.getLogger(GlobalScreen.class.getPackage().getName());
			logger.setLevel(Level.OFF);

			// Don't forget to disable the parent handlers.
			logger.setUseParentHandlers(false);
			
		} catch (Exception e) {
			
		}
	}

	@Override
	public void nativeKeyPressed(NativeKeyEvent arg0) {
		String pressed = NativeKeyEvent.getKeyText(arg0.getKeyCode());
		System.out.println("Key pressed: " + pressed);
		
		if (pressed.equalsIgnoreCase("r")) {
			Robot bot;
			try {
				bot = new Robot();
				
				for (int i = 0 ; i < 100 ; i++) {
					System.out.println("Click");
					bot.mousePress(InputEvent.BUTTON1_MASK);
				    bot.mouseRelease(InputEvent.BUTTON1_MASK);					
				}
			} catch (AWTException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			
			
		}
	}

	@Override
	public void nativeKeyReleased(NativeKeyEvent arg0) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public void nativeKeyTyped(NativeKeyEvent arg0) {
		// TODO Auto-generated method stub
		
	}

}
